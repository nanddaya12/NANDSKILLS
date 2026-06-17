<?php

namespace App\Livewire\Lms;

use Livewire\Component;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\ExamViolation;
use Carbon\Carbon;

class ExamPlayer extends Component
{
    public ?QuizAttempt $attempt = null;
    public $quiz;
    public $questions = [];
    public array $answers = [];
    
    // Timer details
    public int $timeLeftSeconds = 0;
    public string $currentIpAddress = '';
    public int $warningCount = 0;

    // Listeners for client-side events
    protected $listeners = [
        'cheatingDetected' => 'logCheatingViolation',
        'timerExpired' => 'submitExam'
    ];

    public function mount($attemptId)
    {
        $this->attempt = QuizAttempt::with(['quiz.questions', 'user'])->findOrFail($attemptId);
        
        if ($this->attempt->status === 'COMPLETED') {
            return redirect()->route('classroom')->with('error', 'This exam attempt has already been submitted.');
        }

        $this->quiz = $this->attempt->quiz;
        $this->questions = $this->quiz->questions;
        
        // Restore saved answers if any
        $this->answers = $this->attempt->answers ?? [];

        // Calculate time limit
        $timeLimitMin = $this->quiz->time_limit_minutes;
        if ($timeLimitMin > 0) {
            $elapsedSeconds = now()->diffInSeconds($this->attempt->started_at);
            $totalSecondsAllowed = $timeLimitMin * 60;
            $this->timeLeftSeconds = max(0, $totalSecondsAllowed - $elapsedSeconds);

            if ($this->timeLeftSeconds <= 0) {
                $this->submitExam();
                return;
            }
        } else {
            $this->timeLeftSeconds = 999999; // No limit
        }

        $this->currentIpAddress = request()->ip();
        
        // Log starting IP address
        $this->checkIpChange();
    }

    public function checkIpChange()
    {
        $lastIp = $this->attempt->answers['_ip'] ?? null;
        if ($lastIp && $lastIp !== $this->currentIpAddress) {
            $this->logCheatingViolation('IP_CHANGE', "User IP address changed from {$lastIp} to {$this->currentIpAddress}");
        }
        
        // Save active IP in answers payload
        $this->answers['_ip'] = $this->currentIpAddress;
        $this->saveDraftAnswers();
    }

    public function saveAnswer($questionId, $value)
    {
        $this->answers[$questionId] = $value;
        $this->saveDraftAnswers();
    }

    public function saveDraftAnswers()
    {
        if ($this->attempt && $this->attempt->status === 'IN_PROGRESS') {
            $this->attempt->update([
                'answers' => $this->answers
            ]);
        }
    }

    public function logCheatingViolation($type, $details = '')
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if (!$tenantId || !$this->attempt) return;

        ExamViolation::create([
            'tenant_id' => $tenantId,
            'user_id' => auth()->id(),
            'quiz_attempt_id' => $this->attempt->id,
            'violation_type' => $type,
            'ip_address' => $this->currentIpAddress,
            'user_agent' => request()->userAgent(),
            'details' => $details,
        ]);

        $this->warningCount++;

        // Auto-submit exam if too many focus losses
        if ($this->warningCount >= 3) {
            $this->submitExam("Exam auto-submitted due to 3 consecutive proctoring violations: " . $type);
        }
    }

    public function submitExam($submitReason = '')
    {
        if (!$this->attempt || $this->attempt->status === 'COMPLETED') return;

        $score = 0;
        $totalPointsAvailable = 0;
        
        foreach ($this->questions as $question) {
            $totalPointsAvailable += $question->points;
            $studentAnswer = $this->answers[$question->id] ?? null;
            $correctAnswer = $question->correct_answer;

            $isCorrect = false;

            if ($question->type === 'MCQ' || $question->type === 'TRUE_FALSE') {
                if (is_array($correctAnswer)) {
                    $isCorrect = in_array($studentAnswer, $correctAnswer);
                } else {
                    $isCorrect = (strcasecmp(trim($studentAnswer), trim($correctAnswer)) === 0);
                }
            } elseif ($question->type === 'MULTIPLE_CHOICE') {
                // Check if all correct options are selected
                if (is_array($studentAnswer) && is_array($correctAnswer)) {
                    sort($studentAnswer);
                    sort($correctAnswer);
                    $isCorrect = ($studentAnswer === $correctAnswer);
                }
            } elseif ($question->type === 'FILL_IN_BLANKS') {
                $isCorrect = (strcasecmp(trim($studentAnswer), trim($correctAnswer)) === 0);
            }

            if ($isCorrect) {
                $score += $question->points;
            } else {
                // Apply negative marking penalty of 0.25 points for incorrect options in MCQs
                if ($question->type === 'MCQ' && $studentAnswer !== null) {
                    $score -= 0.25;
                }
            }
        }

        // Keep score >= 0
        $finalScore = max(0, $score);
        $scorePercentage = $totalPointsAvailable > 0 ? round(($finalScore / $totalPointsAvailable) * 100) : 0;

        $this->attempt->update([
            'status' => 'COMPLETED',
            'score' => $scorePercentage,
            'completed_at' => now(),
            'answers' => $this->answers
        ]);

        session()->flash('exam_ended', true);
        session()->flash('exam_score', $scorePercentage);
        session()->flash('exam_reason', $submitReason);
    }

    public function render()
    {
        return view('livewire.lms.exam-player')
            ->layout('layouts.app');
    }
}
