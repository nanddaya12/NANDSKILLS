<?php

namespace App\Livewire\Reports;

use Livewire\Component;

class ReportCenter extends Component
{
    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Unauthorized. Reports are restricted to Administrators.');
        }
    }

    public function render()
    {
        return view('livewire.reports.report-center')->layout('layouts.app');
    }
}
