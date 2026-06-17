<?php

namespace App\Livewire\Compliance;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ComplianceFramework;
use App\Models\ComplianceRequirement;
use App\Models\ComplianceEvidence;
use App\Models\ComplianceAudit;
use App\Models\CorrectiveAction;
use Illuminate\Support\Facades\Storage;

class ComplianceDashboard extends Component
{
    use WithFileUploads;

    public string $activeTab = 'frameworks';  // frameworks | requirements | evidence | audits | actions

    // ── Framework form ─────────────────────────────────────────────────────
    public bool   $isFrameworkFormOpen = false;
    public string $framework_id        = '';
    public string $fw_name             = '';
    public string $fw_agency           = '';
    public string $fw_version          = '';
    public string $fw_description      = '';
    public string $fw_valid_from       = '';
    public string $fw_valid_until      = '';
    public string $fw_status           = 'ACTIVE';

    // ── Requirement form ────────────────────────────────────────────────────
    public bool   $isRequirementFormOpen = false;
    public string $req_framework_id      = '';
    public string $req_code              = '';
    public string $req_category          = 'ACADEMIC';
    public string $req_title             = '';
    public string $req_description       = '';
    public string $req_status            = 'PENDING';
    public bool   $req_evidence_required = true;

    // ── Audit form ──────────────────────────────────────────────────────────
    public bool   $isAuditFormOpen    = false;
    public string $audit_framework_id = '';
    public string $audit_date         = '';
    public string $audit_auditor      = '';
    public string $audit_type         = 'INTERNAL';
    public string $audit_result       = 'PENDING';
    public string $audit_findings     = '';

    // ── Corrective Action form ──────────────────────────────────────────────
    public bool   $isActionFormOpen       = false;
    public string $action_audit_id        = '';
    public string $action_requirement_id  = '';
    public string $action_title           = '';
    public string $action_description     = '';
    public string $action_due_date        = '';
    public string $action_priority        = 'MEDIUM';
    public string $action_status          = 'OPEN';

    // ── Evidence upload ─────────────────────────────────────────────────────
    public bool   $isEvidenceFormOpen = false;
    public string $ev_requirement_id  = '';
    public string $ev_title           = '';
    public string $ev_description     = '';
    public $ev_file;

    public function mount(): void
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Compliance Center is restricted.');
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->closeForms();
    }

    private function closeForms(): void
    {
        $this->isFrameworkFormOpen = false;
        $this->isRequirementFormOpen = false;
        $this->isAuditFormOpen = false;
        $this->isActionFormOpen = false;
        $this->isEvidenceFormOpen = false;
    }

    // ── Framework CRUD ──────────────────────────────────────────────────────
    public function saveFramework(): void
    {
        $this->validate([
            'fw_name'   => 'required|string|max:150',
            'fw_agency' => 'nullable|string|max:100',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        ComplianceFramework::updateOrCreate(
            ['id' => $this->framework_id ?: null],
            [
                'tenant_id'        => $tenantId,
                'name'             => $this->fw_name,
                'agency'           => $this->fw_agency ?: null,
                'standard_version' => $this->fw_version ?: null,
                'description'      => $this->fw_description ?: null,
                'valid_from'       => $this->fw_valid_from ?: null,
                'valid_until'      => $this->fw_valid_until ?: null,
                'status'           => $this->fw_status,
            ]
        );

        $this->isFrameworkFormOpen = false;
        $this->reset(['framework_id', 'fw_name', 'fw_agency', 'fw_version', 'fw_description']);
    }

    public function editFramework(string $id): void
    {
        $fw = ComplianceFramework::findOrFail($id);
        $this->framework_id  = $fw->id;
        $this->fw_name       = $fw->name;
        $this->fw_agency     = $fw->agency ?? '';
        $this->fw_version    = $fw->standard_version ?? '';
        $this->fw_description = $fw->description ?? '';
        $this->fw_valid_from = $fw->valid_from ?? '';
        $this->fw_valid_until = $fw->valid_until ?? '';
        $this->fw_status     = $fw->status;
        $this->isFrameworkFormOpen = true;
        $this->activeTab = 'frameworks';
    }

    public function deleteFramework(string $id): void
    {
        ComplianceFramework::destroy($id);
    }

    // ── Requirement CRUD ─────────────────────────────────────────────────────
    public function saveRequirement(): void
    {
        $this->validate([
            'req_framework_id' => 'required|uuid',
            'req_title'        => 'required|string|max:200',
            'req_category'     => 'required|string',
        ]);

        ComplianceRequirement::create([
            'framework_id'      => $this->req_framework_id,
            'requirement_code'  => $this->req_code ?: null,
            'category'          => $this->req_category,
            'title'             => $this->req_title,
            'description'       => $this->req_description ?: null,
            'status'            => $this->req_status,
            'evidence_required' => $this->req_evidence_required,
        ]);

        $this->isRequirementFormOpen = false;
        $this->reset(['req_code', 'req_title', 'req_description', 'req_category', 'req_framework_id']);
    }

    public function updateRequirementStatus(string $id, string $status): void
    {
        ComplianceRequirement::findOrFail($id)->update(['status' => $status]);
    }

    // ── Evidence ─────────────────────────────────────────────────────────────
    public function saveEvidence(): void
    {
        $this->validate([
            'ev_requirement_id' => 'required|uuid',
            'ev_title'          => 'required|string|max:200',
            'ev_file'           => 'nullable|file|max:20480',
        ]);

        $path = null;
        if ($this->ev_file) {
            $path = $this->ev_file->store('compliance_evidence', 'local');
        }

        ComplianceEvidence::create([
            'requirement_id' => $this->ev_requirement_id,
            'title'          => $this->ev_title,
            'description'    => $this->ev_description ?: null,
            'file_path'      => $path,
            'uploaded_by'    => auth()->id(),
        ]);

        $this->isEvidenceFormOpen = false;
        $this->reset(['ev_requirement_id', 'ev_title', 'ev_description', 'ev_file']);
    }

    public function verifyEvidence(string $id): void
    {
        ComplianceEvidence::findOrFail($id)->update([
            'is_verified' => true,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);
    }

    // ── Audit CRUD ────────────────────────────────────────────────────────────
    public function saveAudit(): void
    {
        $this->validate([
            'audit_date' => 'required|date',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        ComplianceAudit::create([
            'tenant_id'    => $tenantId,
            'framework_id' => $this->audit_framework_id ?: null,
            'audit_date'   => $this->audit_date,
            'auditor_name' => $this->audit_auditor ?: null,
            'audit_type'   => $this->audit_type,
            'result'       => $this->audit_result,
            'findings'     => $this->audit_findings ?: null,
        ]);

        $this->isAuditFormOpen = false;
        $this->reset(['audit_framework_id', 'audit_date', 'audit_auditor', 'audit_findings']);
    }

    // ── Corrective Actions ────────────────────────────────────────────────────
    public function saveCorrectiveAction(): void
    {
        $this->validate([
            'action_title'       => 'required|string|max:200',
            'action_description' => 'required|string',
        ]);

        CorrectiveAction::create([
            'audit_id'            => $this->action_audit_id ?: null,
            'requirement_id'      => $this->action_requirement_id ?: null,
            'title'               => $this->action_title,
            'description'         => $this->action_description,
            'due_date'            => $this->action_due_date ?: null,
            'priority'            => $this->action_priority,
            'status'              => 'OPEN',
        ]);

        $this->isActionFormOpen = false;
        $this->reset(['action_title', 'action_description', 'action_due_date']);
    }

    public function closeAction(string $id, string $notes): void
    {
        CorrectiveAction::findOrFail($id)->update([
            'status'           => 'CLOSED',
            'resolution_notes' => $notes,
            'closed_at'        => now(),
        ]);
    }

    public function render()
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        $frameworks   = ComplianceFramework::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->withCount('requirements')
            ->orderBy('status')
            ->get();

        $requirements = ComplianceRequirement::when(
            request()->has('fw'),
            fn($q) => $q->where('framework_id', request('fw'))
        )->with('framework')->orderBy('requirement_code')->get();

        $evidences = ComplianceEvidence::with('requirement.framework', 'uploader:id,name')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        $audits = ComplianceAudit::when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with('framework')
            ->orderByDesc('audit_date')
            ->get();

        $actions = CorrectiveAction::whereIn(
            'audit_id',
            $audits->pluck('id')
        )->orderBy('due_date')->get();

        $categories = ['ACADEMIC', 'INFRASTRUCTURE', 'FACULTY', 'RESEARCH', 'GOVERNANCE', 'FINANCIAL', 'STUDENT_AFFAIRS'];

        // Progress computation
        $totalReqs  = ComplianceRequirement::whereIn('framework_id', $frameworks->pluck('id'))->count();
        $metReqs    = ComplianceRequirement::whereIn('framework_id', $frameworks->pluck('id'))->where('status', 'MET')->count();
        $overallPct = $totalReqs > 0 ? round(($metReqs / $totalReqs) * 100) : 0;

        return view('livewire.compliance.compliance-dashboard', compact(
            'frameworks', 'requirements', 'evidences', 'audits', 'actions', 'categories', 'totalReqs', 'metReqs', 'overallPct'
        ))->layout('layouts.app');
    }
}
