<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Attendance;
use App\Models\ExamResult;
use App\Models\Invoice;
use App\Models\StudentProfile;
use App\Models\Payroll;
use App\Models\BookLoan;
use Carbon\Carbon;

/**
 * ReportExporter — generates CSV and printable HTML reports for
 * attendance sheets, exam grade records, fee/payment ledgers, and inventory checkouts.
 */
class ReportExporter extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Attendance Report — CSV export of attendance records in a date range.
     */
    public function attendanceReport(Request $request)
    {
        $tenant = app('currentTenant');
        if (!$tenant) {
            abort(403, 'No active tenant context.');
        }

        $from = $request->get('from', Carbon::now()->startOfMonth()->toDateString());
        $to   = $request->get('to',   Carbon::now()->endOfMonth()->toDateString());

        $records = Attendance::with('user')
            ->where('tenant_id', $tenant->id)
            ->whereBetween('date', [$from, $to])
            ->orderBy('date')
            ->get();

        $rows   = [];
        $rows[] = ['Date', 'Student Name', 'Email', 'Status', 'IP Address'];

        foreach ($records as $rec) {
            $rows[] = [
                $rec->date->toDateString(),
                $rec->user ? ($rec->user->first_name . ' ' . $rec->user->last_name) : 'N/A',
                $rec->user?->email ?? '',
                $rec->status,
                $rec->ip_address ?? '',
            ];
        }

        $filename = "attendance_report_{$from}_{$to}.csv";
        return $this->csvResponse($filename, $rows);
    }

    /**
     * Exam Results Report — CSV export of all student exam grades.
     */
    public function examReport(Request $request)
    {
        $tenant = app('currentTenant');
        if (!$tenant) abort(403);

        $records = ExamResult::with(['exam.course', 'user'])
            ->where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $rows   = [];
        $rows[] = ['Student Name', 'Email', 'Course', 'Exam', 'Marks Obtained', 'Grade', 'GPA', 'Status'];

        foreach ($records as $res) {
            $rows[] = [
                $res->user ? ($res->user->first_name . ' ' . $res->user->last_name) : 'N/A',
                $res->user?->email ?? '',
                $res->exam?->course?->title ?? 'N/A',
                $res->exam?->title ?? 'N/A',
                $res->marks_obtained,
                $res->grade ?? '',
                $res->gpa ?? '',
                $res->status,
            ];
        }

        return $this->csvResponse('exam_results_report.csv', $rows);
    }

    /**
     * Financial Ledger Report — CSV export of all invoices and payment statuses.
     */
    public function financialReport(Request $request)
    {
        $tenant = app('currentTenant');
        if (!$tenant) abort(403);

        $records = Invoice::with(['studentFee.student.user'])
            ->where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $rows   = [];
        $rows[] = ['Invoice #', 'Student Name', 'Total', 'Discount', 'Tax', 'Net', 'Status', 'Due Date'];

        foreach ($records as $inv) {
            $student = $inv->studentFee?->student;
            $studentName = $student?->user ? ($student->user->first_name . ' ' . $student->user->last_name) : 'N/A';

            $rows[] = [
                $inv->invoice_number ?? '',
                $studentName,
                number_format($inv->total, 2),
                number_format($inv->discount ?? 0, 2),
                number_format($inv->tax ?? 0, 2),
                number_format($inv->net ?? 0, 2),
                $inv->status,
                $inv->due_date?->toDateString() ?? '',
            ];
        }

        return $this->csvResponse('financial_ledger_report.csv', $rows);
    }

    /**
     * Student Directory Report — CSV of all enrolled students.
     */
    public function studentDirectoryReport(Request $request)
    {
        $tenant = app('currentTenant');
        if (!$tenant) abort(403);

        $students = StudentProfile::with('user')
            ->where('tenant_id', $tenant->id)
            ->orderBy('created_at')
            ->get();

        $rows   = [];
        $rows[] = ['Roll Number', 'First Name', 'Last Name', 'Email', 'Phone', 'Admission Date', 'Status'];

        foreach ($students as $student) {
            $rows[] = [
                $student->roll_number ?? '',
                $student->user?->first_name ?? '',
                $student->user?->last_name ?? '',
                $student->user?->email ?? '',
                $student->user?->phone ?? '',
                $student->admission_date?->toDateString() ?? '',
                $student->status ?? '',
            ];
        }

        return $this->csvResponse('student_directory_report.csv', $rows);
    }

    /**
     * Payroll Ledger Report — CSV of employee payroll payments.
     */
    public function payrollReport(Request $request)
    {
        $tenant = app('currentTenant');
        if (!$tenant) abort(403);

        $records = Payroll::with('employee.user')
            ->where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $rows   = [];
        $rows[] = ['Employee Name', 'Amount', 'Bonus', 'Deductions', 'Net Pay', 'Status', 'Paid At'];

        foreach ($records as $payroll) {
            $name = $payroll->employee?->user ? ($payroll->employee->user->first_name . ' ' . $payroll->employee->user->last_name) : 'N/A';
            $net  = ($payroll->amount + $payroll->bonus) - $payroll->deductions;
            $rows[] = [
                $name,
                number_format($payroll->amount, 2),
                number_format($payroll->bonus, 2),
                number_format($payroll->deductions, 2),
                number_format($net, 2),
                $payroll->status,
                $payroll->paid_at?->toDateString() ?? 'Pending',
            ];
        }

        return $this->csvResponse('payroll_ledger_report.csv', $rows);
    }

    /**
     * Low-Attendance Risk Report — students with attendance below threshold.
     */
    public function lowAttendanceReport(Request $request)
    {
        $tenant    = app('currentTenant');
        if (!$tenant) abort(403);

        $threshold = (int) $request->get('threshold', 75);

        // Get all students with their attendance statistics
        $students = StudentProfile::with('user')
            ->where('tenant_id', $tenant->id)
            ->get();

        $rows   = [];
        $rows[] = ['Roll Number', 'Student Name', 'Email', 'Total Classes', 'Present', 'Attendance %', 'Risk Level'];

        foreach ($students as $student) {
            if (!$student->user) continue;

            $total   = Attendance::where('user_id', $student->user->id)->count();
            $present = Attendance::where('user_id', $student->user->id)->where('status', 'PRESENT')->count();

            if ($total === 0) continue;

            $percentage = round(($present / $total) * 100, 2);
            $risk       = $percentage < 60 ? 'HIGH' : ($percentage < $threshold ? 'MEDIUM' : 'LOW');

            if ($percentage < $threshold) {
                $rows[] = [
                    $student->roll_number ?? '',
                    $student->user->first_name . ' ' . $student->user->last_name,
                    $student->user->email,
                    $total,
                    $present,
                    $percentage . '%',
                    $risk,
                ];
            }
        }

        if (count($rows) === 1) {
            $rows[] = ['', 'No students below ' . $threshold . '% threshold', '', '', '', '', ''];
        }

        return $this->csvResponse("low_attendance_risk_report_{$threshold}pct.csv", $rows);
    }

    /**
     * Helper: build a CSV download response from an array of row arrays.
     */
    private function csvResponse(string $filename, array $rows): Response
    {
        $output = '';
        foreach ($rows as $row) {
            $escapedRow = array_map(fn($cell) => '"' . str_replace('"', '""', (string) $cell) . '"', $row);
            $output    .= implode(',', $escapedRow) . "\r\n";
        }

        return response($output, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
