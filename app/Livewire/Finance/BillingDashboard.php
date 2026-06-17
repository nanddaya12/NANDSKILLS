<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\StudentProfile;
use App\Models\FeeStructure;
use App\Models\StudentFee;
use App\Models\Payment;
use Illuminate\Support\Str;

class BillingDashboard extends Component
{
    public $invoices = [];
    public $students = [];
    public $feeStructures = [];
    
    // Fee allocation form fields
    public string $selectedStudent = '';
    public string $selectedFeeStructure = '';
    public float $discount = 0.00;
    public float $scholarship = 0.00;
    public bool $isIssuingBill = false;

    protected array $rules = [
        'selectedStudent' => 'required|exists:student_profiles,id',
        'selectedFeeStructure' => 'required|exists:fee_structures,id',
        'discount' => 'required|numeric|min:0',
        'scholarship' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Unauthorized action. ERP Finance is restricted to Administrators.');
        }
        $this->loadBillingData();
    }

    public function loadBillingData()
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        if ($tenantId) {
            // Seed sample fee structures if empty
            if (FeeStructure::count() === 0) {
                FeeStructure::create([
                    'tenant_id' => $tenantId,
                    'name' => 'Q2 Tuition Fee',
                    'description' => 'Standard Tuition Fee for active classes.',
                    'amount' => 450.00,
                    'due_date' => now()->addDays(15),
                    'frequency' => 'TERM',
                ]);
            }
        }

        $this->invoices = Invoice::with('studentFee.student.user')->orderBy('created_at', 'desc')->get();
        $this->students = StudentProfile::with('user')->get();
        $this->feeStructures = FeeStructure::all();
    }

    public function issueBill()
    {
        $this->validate();

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        $student = StudentProfile::find($this->selectedStudent);
        $fee = FeeStructure::find($this->selectedFeeStructure);

        if ($student && $fee && $tenantId) {
            $net = max(0, $fee->amount - ($this->discount + $this->scholarship));

            $studentFee = StudentFee::create([
                'student_id' => $student->id,
                'fee_structure_id' => $fee->id,
                'discount_amount' => $this->discount,
                'scholarship_amount' => $this->scholarship,
                'net_amount' => $net,
                'status' => 'UNPAID',
            ]);

            Invoice::create([
                'tenant_id' => $tenantId,
                'student_fee_id' => $studentFee->id,
                'invoice_number' => 'INV-' . date('Y') . '-' . strtoupper(Str::random(4)),
                'amount' => $fee->amount,
                'tax' => 0.00,
                'total' => $net,
                'status' => 'UNPAID',
                'due_date' => $fee->due_date,
            ]);
        }

        $this->resetInputFields();
        $this->loadBillingData();
        $this->isIssuingBill = false;
    }

    public function simulatePayment(string $invoiceId)
    {
        $invoice = Invoice::find($invoiceId);
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        if ($invoice && $tenantId) {
            $invoice->update(['status' => 'PAID']);
            $invoice->studentFee->update(['status' => 'PAID']);

            Payment::create([
                'tenant_id' => $tenantId,
                'invoice_id' => $invoice->id,
                'amount' => $invoice->total,
                'method' => 'STRIPE',
                'transaction_id' => 'ch_' . Str::random(10),
                'status' => 'COMPLETED',
                'paid_at' => now(),
            ]);
        }

        $this->loadBillingData();
    }

    private function resetInputFields()
    {
        $this->selectedStudent = '';
        $this->selectedFeeStructure = '';
        $this->discount = 0.00;
        $this->scholarship = 0.00;
    }

    public function render()
    {
        return view('livewire.finance.billing-dashboard')
            ->layout('layouts.app');
    }
}
