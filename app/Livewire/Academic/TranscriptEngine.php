<?php

namespace App\Livewire\Academic;

use Livewire\Component;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\Enrollment;
use App\Models\ExamResult;
use App\Models\GradingRule;
use App\Models\CgpaRule;
use App\Models\Transcript;
use App\Models\StudentSessionEnrollment;
use Illuminate\Support\Facades\DB;

class TranscriptEngine extends Component
{
    public $students = [];
    public string $selectedStudentId = '';
    public string $viewType = 'official'; // official, semester, degree
    public string $selectedSemesterNo = '1';

    // Calculation states
    public $studentProfile = null;
    public $coursesData = [];
    public float $cgpa = 0.00;
    public float $totalCredits = 0.0;
    public string $academicStanding = 'GOOD';
    public $savedTranscripts = [];

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Unauthorized. Transcript Engine is restricted to Registrar and Admins.');
        }

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if ($tenantId) {
            $this->students = User::where('tenant_id', $tenantId)
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'Student');
                })->get();
        }
    }

    public function loadTranscript()
    {
        $this->validate([
            'selectedStudentId' => 'required|uuid',
        ]);

        $this->studentProfile = StudentProfile::with('user', 'program')
            ->where('user_id', $this->selectedStudentId)
            ->first();

        if (!$this->studentProfile) {
            session()->flash('error', 'Student Profile not found.');
            return;
        }

        $this->calculateGpaData();
        $this->loadSavedTranscripts();
    }

    private function calculateGpaData()
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        // Fetch student's course results
        $results = ExamResult::with(['exam.course'])
            ->where('user_id', $this->selectedStudentId)
            ->get();

        $this->coursesData = [];
        $totalWeight = 0.0;
        $weightedPointsSum = 0.0;
        $creditsSum = 0.0;

        // Fallback grading scale if no rules defined
        $gradingRules = GradingRule::where('tenant_id', $tenantId)->get();

        // Group exam results by course
        $groupedResults = $results->groupBy('exam.course_id');

        foreach ($groupedResults as $courseId => $courseResults) {
            $course = $courseResults->first()->exam->course;
            if (!$course) continue;

            // Compute overall score for course (average of exam scores)
            $totalMarks = 0;
            $obtainedMarks = 0;
            foreach ($courseResults as $res) {
                if ($res->exam) {
                    $totalMarks += $res->exam->total_marks;
                    $obtainedMarks += $res->marks_obtained;
                }
            }

            $overallPct = $totalMarks > 0 ? ($obtainedMarks / $totalMarks) * 100 : 0;

            // Determine grade letter and points
            $gradeLetter = 'F';
            $gradePoints = 0.00;

            if ($gradingRules->isNotEmpty()) {
                $matchedRule = $gradingRules->first(function ($rule) use ($overallPct) {
                    return $overallPct >= $rule->min_score && $overallPct <= $rule->max_score;
                });
                if ($matchedRule) {
                    $gradeLetter = $matchedRule->grade_letter;
                    $gradePoints = (float)$matchedRule->grade_points;
                }
            } else {
                // Fallback standard grading scale
                if ($overallPct >= 90) { $gradeLetter = 'A'; $gradePoints = 4.00; }
                elseif ($overallPct >= 80) { $gradeLetter = 'B'; $gradePoints = 3.00; }
                elseif ($overallPct >= 70) { $gradeLetter = 'C'; $gradePoints = 2.00; }
                elseif ($overallPct >= 60) { $gradeLetter = 'D'; $gradePoints = 1.00; }
                else { $gradeLetter = 'F'; $gradePoints = 0.00; }
            }

            // Assume standard 3 credit hours per course unless program mapping overrides
            $creditHours = 3.0;
            
            $this->coursesData[] = [
                'course_title' => $course->title,
                'course_code' => $course->slug ?? 'CS-101',
                'overall_score' => round($overallPct, 2),
                'grade' => $gradeLetter,
                'points' => $gradePoints,
                'credits' => $creditHours,
                'semester_no' => 1, // Assume sem 1 for simplification in mock, or link to enrollment
            ];

            if ($gradeLetter !== 'F') {
                $creditsSum += $creditHours;
            }

            $weightedPointsSum += ($gradePoints * $creditHours);
            $totalWeight += $creditHours;
        }

        $this->totalCredits = $creditsSum;
        $this->cgpa = $totalWeight > 0 ? round($weightedPointsSum / $totalWeight, 2) : 0.00;

        // Apply CGPA Standing Rules
        $standingRule = CgpaRule::where('tenant_id', $tenantId)
            ->where('min_cgpa', '<=', $this->cgpa)
            ->where('max_cgpa', '>=', $this->cgpa)
            ->first();

        $this->academicStanding = $standingRule ? $standingRule->status_tag : 'GOOD';
    }

    private function loadSavedTranscripts()
    {
        $this->savedTranscripts = Transcript::where('user_id', $this->selectedStudentId)
            ->orderBy('compiled_at', 'desc')
            ->get();
    }

    public function saveTranscriptRecord()
    {
        $this->validate([
            'selectedStudentId' => 'required|uuid',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        Transcript::create([
            'tenant_id' => $tenantId,
            'user_id' => $this->selectedStudentId,
            'cgpa' => $this->cgpa,
            'compiled_at' => now(),
            'file_url' => null, // printed dynamically
        ]);

        $this->loadSavedTranscripts();
        session()->flash('success', 'Transcript compilation record saved successfully.');
    }

    public function render()
    {
        return view('livewire.academic.transcript-engine')
            ->layout('layouts.app');
    }
}
