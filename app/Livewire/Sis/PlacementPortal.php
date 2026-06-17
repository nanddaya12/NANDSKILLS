<?php

namespace App\Livewire\Sis;

use Livewire\Component;
use App\Models\EmployerProfile;
use App\Models\PlacementJob;
use App\Models\PlacementApplication;
use App\Models\StudentProfile;
use Illuminate\Support\Str;

class PlacementPortal extends Component
{
    public $jobs = [];
    public $employers = [];
    public $applications = [];
    public $students = [];

    // Views switches
    public bool $isAdmin = false;
    public string $activeTab = 'jobs'; // 'jobs', 'profile', 'applications', 'candidates'

    // Job Creation Fields
    public string $selectedEmployer = '';
    public string $jobTitle = '';
    public string $jobDescription = '';
    public string $jobRequirements = '';
    public string $jobLocation = '';
    public string $jobType = 'FULL_TIME';
    public string $salaryRange = '';

    // Employer Creation Fields
    public string $companyName = '';
    public string $industry = '';
    public string $website = '';
    public string $companyDescription = '';
    public string $contactEmail = '';

    // Student Apply / Resume Compile Fields
    public string $resumeText = '';
    public ?string $applyingJobId = null;
    public bool $showApplyModal = false;

    // Admin Review Fields
    public ?string $selectedApplicationId = null;
    public string $interviewDate = '';
    public bool $showReviewModal = false;

    protected array $rules = [
        'jobTitle' => 'required_if:activeTab,jobs|string|max:100',
        'jobDescription' => 'required_if:activeTab,jobs|string',
        'companyName' => 'required_if:activeTab,profile|string|max:100',
        'contactEmail' => 'required_if:activeTab,profile|email',
    ];

    public function mount()
    {
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }

        $this->isAdmin = $user->hasRole(['Super Admin', 'Tenant Admin', 'Trainer']);
        $this->loadData();
    }

    public function loadData()
    {
        $tenant = app('currentTenant');
        if (!$tenant) return;

        $this->jobs = PlacementJob::with('employer')->latest()->get();
        $this->employers = EmployerProfile::all();

        // Seed a default employer if empty for testing
        if ($this->employers->isEmpty()) {
            $defaultEmployer = EmployerProfile::create([
                'tenant_id' => $tenant->id,
                'company_name' => 'NANDSKILLS Global Tech',
                'industry' => 'Information Technology',
                'website' => 'https://nandskills.com',
                'description' => 'A premier engineering corporation.',
                'contact_email' => 'careers@nandskills.com',
            ]);
            $this->employers = EmployerProfile::all();
        }

        if ($this->isAdmin) {
            $this->applications = PlacementApplication::with(['job.employer', 'student.user'])->latest()->get();
            $this->students = StudentProfile::with('user')->get();
        } else {
            // Find student profile of authenticated user
            $studentProfile = StudentProfile::where('user_id', auth()->id())->first();
            if ($studentProfile) {
                $this->applications = PlacementApplication::with(['job.employer'])
                    ->where('student_profile_id', $studentProfile->id)
                    ->latest()
                    ->get();
            }
        }
    }

    public function postJob()
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if (!$tenantId || !$this->isAdmin) return;

        $this->validate([
            'selectedEmployer' => 'required|exists:employer_profiles,id',
            'jobTitle' => 'required|string|max:100',
            'jobDescription' => 'required|string',
        ]);

        PlacementJob::create([
            'tenant_id' => $tenantId,
            'employer_profile_id' => $this->selectedEmployer,
            'title' => $this->jobTitle,
            'description' => $this->jobDescription,
            'requirements' => $this->jobRequirements,
            'location' => $this->jobLocation,
            'type' => $this->jobType,
            'salary_range' => $this->salaryRange,
            'status' => 'ACTIVE',
        ]);

        session()->flash('success', 'Job Listing posted successfully.');
        $this->resetJobFields();
        $this->loadData();
    }

    public function registerEmployer()
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if (!$tenantId || !$this->isAdmin) return;

        $this->validate([
            'companyName' => 'required|string|max:100',
            'contactEmail' => 'required|email',
        ]);

        EmployerProfile::create([
            'tenant_id' => $tenantId,
            'company_name' => $this->companyName,
            'industry' => $this->industry,
            'website' => $this->website,
            'description' => $this->companyDescription,
            'contact_email' => $this->contactEmail,
        ]);

        session()->flash('success', 'Company Profile registered successfully.');
        $this->resetEmployerFields();
        $this->loadData();
    }

    public function selectJobToApply($jobId)
    {
        $this->applyingJobId = $jobId;
        $this->showApplyModal = true;
    }

    public function applyToJob()
    {
        $this->validate([
            'resumeText' => 'required|string|min:20',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        $student = StudentProfile::where('user_id', auth()->id())->first();

        if ($tenantId && $student && $this->applyingJobId) {
            // Check if already applied
            $exists = PlacementApplication::where('placement_job_id', $this->applyingJobId)
                ->where('student_profile_id', $student->id)
                ->exists();

            if ($exists) {
                session()->flash('error', 'You have already applied to this listing.');
                $this->showApplyModal = false;
                return;
            }

            PlacementApplication::create([
                'tenant_id' => $tenantId,
                'placement_job_id' => $this->applyingJobId,
                'student_profile_id' => $student->id,
                'resume_text' => $this->resumeText,
                'status' => 'PENDING',
            ]);

            session()->flash('success', 'Application submitted successfully.');
            $this->showApplyModal = false;
            $this->loadData();
        }
    }

    public function selectApplicationToReview($appId)
    {
        $this->selectedApplicationId = $appId;
        $this->showReviewModal = true;
    }

    public function updateApplicationStatus($status)
    {
        $app = PlacementApplication::find($this->selectedApplicationId);
        if ($app) {
            $updateData = ['status' => $status];
            if ($status === 'SHORTLISTED' && $this->interviewDate) {
                $updateData['interview_scheduled_at'] = $this->interviewDate;
            }
            $app->update($updateData);

            session()->flash('success', "Application status updated to {$status}.");
            $this->showReviewModal = false;
            $this->loadData();
        }
    }

    private function resetJobFields()
    {
        $this->selectedEmployer = '';
        $this->jobTitle = '';
        $this->jobDescription = '';
        $this->jobRequirements = '';
        $this->jobLocation = '';
        $this->jobType = 'FULL_TIME';
        $this->salaryRange = '';
    }

    private function resetEmployerFields()
    {
        $this->companyName = '';
        $this->industry = '';
        $this->website = '';
        $this->companyDescription = '';
        $this->contactEmail = '';
    }

    public function render()
    {
        return view('livewire.sis.placement-portal')
            ->layout('layouts.app');
    }
}
