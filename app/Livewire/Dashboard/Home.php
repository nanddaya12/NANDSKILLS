<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Payment;
use App\Models\Course;
use App\Models\StudentProfile;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use App\Models\Assignment;
use App\Models\Certificate;

class Home extends Component
{
    public function render()
    {
        $user = auth()->user();

        if ($user->hasRole('Super Admin')) {
            $totalTenants = Tenant::count();
            $monthlyRevenue = Payment::where('status', 'COMPLETED')
                ->where('created_at', '>=', now()->startOfMonth())
                ->sum('amount');
            $activeUsers = User::where('status', 'ACTIVE')->count();
            $tenants = Tenant::with('plan')->get();

            return view('livewire.dashboard.super-admin', [
                'totalTenants' => $totalTenants,
                'monthlyRevenue' => $monthlyRevenue,
                'activeUsers' => $activeUsers,
                'tenants' => $tenants,
            ])->layout('layouts.app');
        }

        if ($user->hasRole('Tenant Admin')) {
            $studentCount = StudentProfile::count();
            $trainerCount = User::whereHas('roles', function($q) {
                $q->where('name', 'Trainer');
            })->count();
            $courseCount = Course::count();
            $unpaidInvoiceSum = Invoice::where('status', 'UNPAID')->sum('total');
            $activePrograms = Course::withCount('enrollments')->latest()->take(4)->get();
            
            // CRM lead status counts
            $newLeads = Lead::where('status', 'NEW')->count();
            $workingLeads = Lead::where('status', 'WORKING')->count();
            $wonLeads = Lead::where('status', 'WON')->count();
            $totalLeads = Lead::count();

            return view('livewire.dashboard.tenant-admin', [
                'studentCount' => $studentCount,
                'trainerCount' => $trainerCount,
                'courseCount' => $courseCount,
                'unpaidInvoiceSum' => $unpaidInvoiceSum,
                'activePrograms' => $activePrograms,
                'newLeads' => $newLeads,
                'workingLeads' => $workingLeads,
                'wonLeads' => $wonLeads,
                'totalLeads' => $totalLeads,
            ])->layout('layouts.app');
        }

        if ($user->hasRole('Trainer')) {
            $courses = Course::withCount('enrollments')->get();
            $pendingSubmissions = AssignmentSubmission::with(['assignment.lesson.chapter.course', 'user'])
                ->where('status', 'SUBMITTED')
                ->latest()
                ->get();

            return view('livewire.dashboard.trainer', [
                'courses' => $courses,
                'pendingSubmissions' => $pendingSubmissions,
            ])->layout('layouts.app');
        }

        if ($user->hasRole('Student')) {
            $enrollments = Enrollment::with(['course.chapters.lessons'])->where('user_id', $user->id)->get();
            
            $deadlines = Assignment::whereHas('lesson.chapter.course.enrollments', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest()
            ->take(3)
            ->get();

            $certificates = Certificate::with('course')->where('user_id', $user->id)->get();

            return view('livewire.dashboard.student', [
                'enrollments' => $enrollments,
                'deadlines' => $deadlines,
                'certificates' => $certificates,
            ])->layout('layouts.app');
        }

        if ($user->hasRole('Parent')) {
            $students = StudentProfile::whereHas('parents', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->with(['user', 'enrollments.course'])->get();

            $invoices = Invoice::whereHas('studentFee.student.parents', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->latest()->get();

            return view('livewire.dashboard.parent', [
                'students' => $students,
                'invoices' => $invoices,
            ])->layout('layouts.app');
        }

        abort(403, 'Unauthorized role access.');
    }
}
