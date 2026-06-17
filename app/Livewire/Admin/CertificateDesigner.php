<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\CertificateTemplate;

class CertificateDesigner extends Component
{
    public $templates = [];
    public ?string $selectedTemplateId = null;
    
    // Editor fields
    public string $templateName = '';
    public string $htmlContent = '';
    public bool $isActive = true;

    protected array $rules = [
        'templateName' => 'required|string|max:100',
        'htmlContent' => 'required|string',
        'isActive' => 'required|boolean',
    ];

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Unauthorized action. Certificate designer is restricted to Administrators.');
        }

        $this->loadTemplates();
    }

    public function loadTemplates()
    {
        $tenant = app('currentTenant');
        if (!$tenant) return;

        $this->templates = CertificateTemplate::where('tenant_id', $tenant->id)->get();

        if ($this->selectedTemplateId) {
            $current = $this->templates->firstWhere('id', $this->selectedTemplateId);
            if ($current) {
                $this->templateName = $current->name;
                $this->htmlContent = $current->content_html ?? '';
                $this->isActive = $current->is_active;
            }
        } elseif ($this->templates->isNotEmpty()) {
            $this->selectTemplate($this->templates[0]->id);
        } else {
            // Seed a default template if empty
            $default = CertificateTemplate::create([
                'tenant_id' => $tenant->id,
                'name' => 'Standard Certificate',
                'content_html' => '<div style="border:10px solid #2B6CB0; padding:50px; background:#fff; text-align:center;">' .
                    '<h1 style="color:#2B6CB0; font-size:42px; font-weight:bold;">CERTIFICATE OF COMPLETION</h1>' .
                    '<p style="font-size:18px; margin-top:20px;">This is proudly presented to</p>' .
                    '<h2 style="font-size:28px; font-weight:extrabold;">{{student_name}}</h2>' .
                    '<p style="font-size:18px;">for successfully completing the training program in</p>' .
                    '<h3 style="font-size:22px; color:#2D3748; font-weight:bold;">{{course_title}}</h3>' .
                    '<p style="font-size:14px; margin-top:30px;">Certificate Code: <strong>{{certificate_code}}</strong> | Date: {{issue_date}}</p>' .
                    '</div>',
                'is_active' => true,
            ]);
            $this->selectTemplate($default->id);
        }
    }

    public function selectTemplate($templateId)
    {
        $this->selectedTemplateId = $templateId;
        $current = CertificateTemplate::find($templateId);
        if ($current) {
            $this->templateName = $current->name;
            $this->htmlContent = $current->content_html ?? '';
            $this->isActive = $current->is_active;
        }
    }

    public function createTemplate()
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if (!$tenantId) return;

        $new = CertificateTemplate::create([
            'tenant_id' => $tenantId,
            'name' => 'New Template ' . (count($this->templates) + 1),
            'content_html' => '<div style="border:5px solid #1A365D; padding:40px; text-align:center;"><h1>Certificate Template</h1></div>',
            'is_active' => true,
        ]);

        $this->selectedTemplateId = $new->id;
        $this->loadTemplates();
        session()->flash('success', 'New template created.');
    }

    public function saveTemplate()
    {
        $this->validate();

        $template = CertificateTemplate::find($this->selectedTemplateId);
        if ($template) {
            $template->update([
                'name' => $this->templateName,
                'content_html' => $this->htmlContent,
                'is_active' => $this->isActive,
            ]);

            session()->flash('success', 'Certificate template updated successfully.');
            $this->loadTemplates();
        }
    }

    public function render()
    {
        return view('livewire.admin.certificate-designer')
            ->layout('layouts.app');
    }
}
