<?php

namespace App\Livewire\Admissions;

use Livewire\Component;
use App\Models\Program;
use App\Models\AcademicSession;
use App\Models\Admission;
use App\Models\MeritList as MeritListModel;

class MeritList extends Component
{
    public $programs = [];
    public $sessions = [];
    public $meritItems = [];

    public string $selectedProgramId = '';
    public string $selectedSessionId = '';

    public string $statusMessage = '';

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin', 'Trainer'])) {
            abort(403, 'Merit Lists are restricted to staff.');
        }

        $this->programs = Program::where('status', 'ACTIVE')->get();
        $this->sessions = AcademicSession::orderBy('start_date', 'desc')->get();
    }

    public function loadMeritList()
    {
        $this->validate([
            'selectedProgramId' => 'required|uuid',
            'selectedSessionId' => 'required|uuid',
        ]);

        $this->meritItems = MeritListModel::with('admission')
            ->where('program_id', $this->selectedProgramId)
            ->where('academic_session_id', $this->selectedSessionId)
            ->orderBy('rank')
            ->get();

        $this->statusMessage = '';
    }

    public function generateMeritList()
    {
        $this->validate([
            'selectedProgramId' => 'required|uuid',
            'selectedSessionId' => 'required|uuid',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        // Fetch all SUBMITTED, UNDER_REVIEW, or INTERVIEW_SCHEDULED admissions for this program
        $admissions = Admission::where('program_id', $this->selectedProgramId)
            ->whereIn('status', ['SUBMITTED', 'UNDER_REVIEW', 'INTERVIEW_SCHEDULED', 'APPROVED'])
            ->get();

        if ($admissions->isEmpty()) {
            $this->addError('merit', 'No applicants found for ranking.');
            return;
        }

        // Calculate merit score: e.g. previous qualification grade (100%) + any interview score
        $ranked = [];
        foreach ($admissions as $adm) {
            $interviewScore = $adm->interviews()->where('status', 'COMPLETED')->avg('score') ?? 0;
            // Let's say merit score = previous qualification grade + (interview score * 10)
            $score = $adm->previous_grade + ($interviewScore * 2);

            $ranked[] = [
                'admission_id' => $adm->id,
                'score' => min(100.00, $score),
            ];
        }

        // Sort by score desc
        usort($ranked, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // Delete existing merit list for this cohort
        MeritListModel::where('program_id', $this->selectedProgramId)
            ->where('academic_session_id', $this->selectedSessionId)
            ->delete();

        // Create new ranked lists
        foreach ($ranked as $index => $item) {
            MeritListModel::create([
                'tenant_id' => $tenantId,
                'program_id' => $this->selectedProgramId,
                'academic_session_id' => $this->selectedSessionId,
                'admission_id' => $item['admission_id'],
                'merit_score' => $item['score'],
                'rank' => $index + 1,
                'status' => 'LISTED',
            ]);
        }

        $this->statusMessage = 'Merit list generated and ranked successfully!';
        $this->loadMeritList();
    }

    public function bulkOffer(int $topLimit)
    {
        if (empty($this->meritItems)) {
            return;
        }

        $count = 0;
        foreach ($this->meritItems->take($topLimit) as $item) {
            if ($item->status === 'LISTED') {
                $item->update(['status' => 'OFFERED']);
                $item->admission->update(['status' => 'APPROVED']);
                $count++;
            }
        }

        $this->statusMessage = "Successfully issued admission offers to the top $count applicants!";
        $this->loadMeritList();
    }

    public function updateItemStatus(string $id, string $newStatus)
    {
        $item = MeritListModel::findOrFail($id);
        $item->update(['status' => $newStatus]);
        
        if ($newStatus === 'ACCEPTED') {
            $item->admission->update(['status' => 'APPROVED']);
        } elseif ($newStatus === 'DECLINED') {
            $item->admission->update(['status' => 'REJECTED']);
        }

        $this->loadMeritList();
    }

    public function render()
    {
        return view('livewire.admissions.merit-list')
            ->layout('layouts.app');
    }
}
