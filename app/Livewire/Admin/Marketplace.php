<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Tenant;

class Marketplace extends Component
{
    public array $availableModules = [
        'lms' => [
            'name' => 'LMS Course Management',
            'desc' => 'Interactive course player, quiz engines, certifications builder, and skill tree gates.',
            'category' => 'Academics',
            'icon' => '🎓',
        ],
        'crm' => [
            'name' => 'CRM Sales & Admissions',
            'desc' => 'Admissions lead pipelines, sales trackers, parent inquiries, and visual Kanban boards.',
            'category' => 'Marketing',
            'icon' => '📈',
        ],
        'sis' => [
            'name' => 'Student Information System (SIS)',
            'desc' => 'Student registry profiles, parent associations, daily attendance trackers, and check-ins.',
            'category' => 'Administration',
            'icon' => '👥',
        ],
        'finance' => [
            'name' => 'ERP Financial Invoice & Fees',
            'desc' => 'Tuition fee installments builder, printable receipts generation, and automatic late fees scheduling.',
            'category' => 'Finance',
            'icon' => '💳',
        ],
        'tickets' => [
            'name' => 'Helpdesk Support Tickets',
            'desc' => 'Multi-agent customer support desk, student grievance queries, and categories routing.',
            'category' => 'Support',
            'icon' => '🎫',
        ],
        'meetings' => [
            'name' => 'Virtual Classrooms & WebRTC',
            'desc' => 'Live-streamed virtual video conferences, signaling rooms, screen sharing, and recording logs.',
            'category' => 'Academics',
            'icon' => '🎥',
        ],
        'hr' => [
            'name' => 'HR & Payroll Systems',
            'desc' => 'Staff registry databases, monthly salary payouts ledger, and employee leave request trackers.',
            'category' => 'Operations',
            'icon' => '💼',
        ],
        'assets' => [
            'name' => 'Inventory & Asset Trackers',
            'desc' => 'Academy hardware assets, equipment checkout registers, and device condition trails.',
            'category' => 'Operations',
            'icon' => '📦',
        ],
        'library' => [
            'name' => 'Library Catalog & Loans',
            'desc' => 'Book inventory tracking, lending checkout counters, and late return fine penalty schedules.',
            'category' => 'Operations',
            'icon' => '📚',
        ],
        'compliance' => [
            'name' => 'Compliance & Audits',
            'desc' => 'Accreditation board frameworks, compliance standards, and document audit checklists.',
            'category' => 'Security',
            'icon' => '🛡️',
        ],
        'api' => [
            'name' => 'API Integrations Platform',
            'desc' => 'Secure REST API endpoint integrations, OAuth configurations, and personal access tokens generator.',
            'category' => 'Security',
            'icon' => '🔑',
        ],
    ];

    public array $activeModules = [];

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Unauthorized action. Marketplace is restricted to Administrators.');
        }

        $tenant = app('currentTenant');
        if ($tenant) {
            $this->activeModules = $tenant->active_modules ?? array_keys($this->availableModules);
        }
    }

    public function toggleModule(string $moduleId)
    {
        $tenant = app('currentTenant');
        if (!$tenant) return;

        $modules = $tenant->active_modules ?? array_keys($this->availableModules);

        if (in_array($moduleId, $modules)) {
            // Disable it (remove from array)
            $modules = array_values(array_diff($modules, [$moduleId]));
            $actionText = 'disabled';
        } else {
            // Enable it (add to array)
            $modules[] = $moduleId;
            $actionText = 'enabled';
        }

        $tenant->update(['active_modules' => $modules]);
        $this->activeModules = $modules;

        session()->flash('status', "Module toggled successfully! " . $this->availableModules[$moduleId]['name'] . " is now " . $actionText . ".");
    }

    public function render()
    {
        return view('livewire.admin.marketplace')->layout('layouts.app');
    }
}
