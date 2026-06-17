<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\StudentFee;
use Carbon\Carbon;

class FeeController extends Controller
{
    public function __construct()
    {
        // Enforce authentication for receipt operations
        $this->middleware('auth')->only('downloadReceipt');
    }

    /**
     * Renders a highly polished printable receipt for the student invoice.
     */
    public function downloadReceipt($invoiceId)
    {
        $invoice = Invoice::with(['studentFee.student.user', 'studentFee.feeStructure', 'tenant', 'payments'])->findOrFail($invoiceId);

        // Render a clean print layout
        return view('finance.receipt', compact('invoice'));
    }

    /**
     * Scan all unpaid invoices that are past their due dates and apply a late fee penalty.
     * Can be run via a scheduler cron or triggered manually by administrators.
     */
    public function applyLateFees()
    {
        $tenant = app('currentTenant');
        if (!$tenant) {
            return response()->json(['error' => 'No active tenant identified.'], 400);
        }

        // Find all unpaid invoices that are past their due date
        $overdueInvoices = Invoice::where('tenant_id', $tenant->id)
            ->where('status', 'UNPAID')
            ->where('due_date', '<', Carbon::today())
            ->get();

        $appliedCount = 0;
        $lateFeeAmount = 15.00; // Flat $15 late fee penalty

        foreach ($overdueInvoices as $invoice) {
            // Update invoice status to OVERDUE
            $invoice->status = 'OVERDUE';
            
            // Add late fee penalty to the total
            $invoice->total += $lateFeeAmount;
            $invoice->tax += $lateFeeAmount; // track penalty here for simulation
            $invoice->save();

            // Also update parent student fee net amount
            $studentFee = $invoice->studentFee;
            if ($studentFee) {
                $studentFee->net_amount += $lateFeeAmount;
                $studentFee->save();
            }

            $appliedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully processed overdue bills. Applied late fee of \${$lateFeeAmount} to {$appliedCount} invoices.",
            'applied_invoices_count' => $appliedCount,
        ]);
    }
}
