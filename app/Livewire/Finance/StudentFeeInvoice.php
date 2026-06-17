<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\StudentFee;
use App\Models\StudentProfile;
use App\Models\FeeStructure;
use App\Models\Invoice;
use Illuminate\Support\Str;

class StudentFeeInvoice extends Component
{
    public $studentFees = [];
    public $students = [];
    public $feeStructures = [];
    public $invoices = [];

    // Issue Fee Fields
    public string $selectedStudent = '';
    public string $selectedFeeStructure = '';
    public float $discount = 0.00;
    public float $scholarship = 0.00;
    public int $installments = 1; // Default 1 installment (full payment)

    // Installment Setup Modal
    public ?StudentFee $selectedStudentFeeForInstallments = null;
    public int $numberOfInstallments = 2;
    public bool $showInstallmentModal = false;

    protected array $rules = [
        'selectedStudent' => 'required|exists:student_profiles,id',
        'selectedFeeStructure' => 'required|exists:fee_structures,id',
        'discount' => 'required|numeric|min:0',
        'scholarship' => 'required|numeric|min:0',
        'installments' => 'required|integer|min:1|max:12',
    ];

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin'])) {
            abort(403, 'Unauthorized action. ERP Finance is restricted to Administrators.');
        }

        $this->loadData();
    }

    public function loadData()
    {
        $this->studentFees = StudentFee::with(['student.user', 'feeStructure', 'invoices'])->latest()->get();
        $this->students = StudentProfile::with('user')->get();
        $this->feeStructures = FeeStructure::all();
        $this->invoices = Invoice::with('studentFee.student.user', 'payments')->latest()->get();
    }

    public function issueFee()
    {
        $this->validate();

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if (!$tenantId) return;

        $student = StudentProfile::find($this->selectedStudent);
        $feeStructure = FeeStructure::find($this->selectedFeeStructure);

        if ($student && $feeStructure) {
            $netAmount = max(0, $feeStructure->amount - ($this->discount + $this->scholarship));

            $studentFee = StudentFee::create([
                'student_id' => $student->id,
                'fee_structure_id' => $feeStructure->id,
                'discount_amount' => $this->discount,
                'scholarship_amount' => $this->scholarship,
                'net_amount' => $netAmount,
                'status' => 'UNPAID',
            ]);

            // Create installments (invoices)
            $installmentAmount = round($netAmount / $this->installments, 2);
            $lastInstallmentAmount = $netAmount - ($installmentAmount * ($this->installments - 1));

            for ($i = 1; $i <= $this->installments; $i++) {
                $amountForThisInvoice = ($i === $this->installments) ? $lastInstallmentAmount : $installmentAmount;
                $dueDate = \Carbon\Carbon::parse($feeStructure->due_date)->addMonths($i - 1);

                Invoice::create([
                    'tenant_id' => $tenantId,
                    'student_fee_id' => $studentFee->id,
                    'invoice_number' => 'INV-' . date('Y') . '-' . strtoupper(Str::random(4)) . '-T' . $i,
                    'amount' => $amountForThisInvoice,
                    'tax' => 0.00,
                    'total' => $amountForThisInvoice,
                    'status' => 'UNPAID',
                    'due_date' => $dueDate,
                ]);
            }

            session()->flash('success', 'Fee allocated and invoices generated successfully.');
            $this->resetFields();
            $this->loadData();
        }
    }

    public function selectFeeForInstallments($studentFeeId)
    {
        $this->selectedStudentFeeForInstallments = StudentFee::with('invoices')->find($studentFeeId);
        $this->showInstallmentModal = true;
    }

    public function applyInstallmentPlan()
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if (!$this->selectedStudentFeeForInstallments || !$tenantId) return;

        // Delete existing unpaid invoices
        Invoice::where('student_fee_id', $this->selectedStudentFeeForInstallments->id)
            ->where('status', 'UNPAID')
            ->delete();

        $remainingAmount = Invoice::where('student_fee_id', $this->selectedStudentFeeForInstallments->id)->where('status', 'PAID')->exists()
            ? $this->selectedStudentFeeForInstallments->net_amount - Invoice::where('student_fee_id', $this->selectedStudentFeeForInstallments->id)->where('status', 'PAID')->sum('total')
            : $this->selectedStudentFeeForInstallments->net_amount;

        if ($remainingAmount <= 0) {
            $this->showInstallmentModal = false;
            return;
        }

        $installmentAmount = round($remainingAmount / $this->numberOfInstallments, 2);
        $lastInstallmentAmount = $remainingAmount - ($installmentAmount * ($this->numberOfInstallments - 1));

        for ($i = 1; $i <= $this->numberOfInstallments; $i++) {
            $amountForThisInvoice = ($i === $this->numberOfInstallments) ? $lastInstallmentAmount : $installmentAmount;
            $dueDate = now()->addMonths($i);

            Invoice::create([
                'tenant_id' => $tenantId,
                'student_fee_id' => $this->selectedStudentFeeForInstallments->id,
                'invoice_number' => 'INV-INST-' . date('Y') . '-' . strtoupper(Str::random(4)) . '-I' . $i,
                'amount' => $amountForThisInvoice,
                'tax' => 0.00,
                'total' => $amountForThisInvoice,
                'status' => 'UNPAID',
                'due_date' => $dueDate,
            ]);
        }

        $this->showInstallmentModal = false;
        session()->flash('success', 'Installment plan applied successfully.');
        $this->loadData();
    }

    private function resetFields()
    {
        $this->selectedStudent = '';
        $this->selectedFeeStructure = '';
        $this->discount = 0.00;
        $this->scholarship = 0.00;
        $this->installments = 1;
    }

    public function render()
    {
        return view('livewire.finance.student-fee-invoice')
            ->layout('layouts.app');
    }
}
