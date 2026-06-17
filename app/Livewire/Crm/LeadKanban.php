<?php

namespace App\Livewire\Crm;

use Livewire\Component;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\CrmActivity;

class LeadKanban extends Component
{
    public $stages = [];
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public float $value = 0.00;
    public string $source = 'Organic';
    public bool $isAddingLead = false;

    protected array $rules = [
        'first_name' => 'required|string|max:50',
        'last_name' => 'required|string|max:50',
        'email' => 'required|email',
        'value' => 'required|numeric|min:0',
        'source' => 'required|string',
    ];

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Unauthorized action. CRM is restricted to Administrators.');
        }
        $this->loadStagesAndLeads();
    }

    public function loadStagesAndLeads()
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        // Ensure default stages exist
        if ($tenantId && PipelineStage::count() === 0) {
            $defaultStages = [
                ['name' => 'New Lead', 'color' => '#3B82F6', 'order' => 1],
                ['name' => 'Contact Attempted', 'color' => '#F59E0B', 'order' => 2],
                ['name' => 'Working', 'color' => '#10B981', 'order' => 3],
                ['name' => 'Qualified', 'color' => '#6366F1', 'order' => 4],
            ];

            foreach ($defaultStages as $s) {
                PipelineStage::create([
                    'tenant_id' => $tenantId,
                    'name' => $s['name'],
                    'color' => $s['color'],
                    'order_index' => $s['order'],
                ]);
            }
        }

        $this->stages = PipelineStage::with(['deals.lead'])->orderBy('order_index')->get();
    }

    public function addLead()
    {
        $this->validate();

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        $firstStage = PipelineStage::orderBy('order_index')->first();

        if ($firstStage) {
            $lead = Lead::create([
                'tenant_id' => $tenantId,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'value' => $this->value,
                'source' => $this->source,
                'status' => 'NEW',
            ]);

            // Create deal in first pipeline stage
            \App\Models\Deal::create([
                'tenant_id' => $tenantId,
                'lead_id' => $lead->id,
                'stage_id' => $firstStage->id,
                'amount' => $this->value,
                'status' => 'OPEN',
            ]);

            // Create initial activity log
            CrmActivity::create([
                'lead_id' => $lead->id,
                'type' => 'NOTE',
                'description' => 'Lead created and added to pipeline.',
                'performed_by_id' => auth()->id(),
            ]);
        }

        $this->resetInputFields();
        $this->loadStagesAndLeads();
        $this->isAddingLead = false;
    }

    public function moveDeal(string $dealId, string $stageId)
    {
        $deal = \App\Models\Deal::find($dealId);
        $stage = PipelineStage::find($stageId);

        if ($deal && $stage) {
            $oldStageName = $deal->stage->name;
            $deal->update(['stage_id' => $stageId]);

            // Log activity
            CrmActivity::create([
                'lead_id' => $deal->lead_id,
                'deal_id' => $deal->id,
                'type' => 'TASK',
                'description' => "Moved opportunity from '{$oldStageName}' to '{$stage->name}'.",
                'performed_by_id' => auth()->id(),
            ]);
        }

        $this->loadStagesAndLeads();
    }

    private function resetInputFields()
    {
        $this->first_name = '';
        $this->last_name = '';
        $this->email = '';
        $this->phone = '';
        $this->value = 0.00;
        $this->source = 'Organic';
    }

    public function render()
    {
        return view('livewire.crm.lead-kanban')
            ->layout('layouts.app');
    }
}
