<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($currentTenant) ? $currentTenant->name : 'NANDSKILLS Dashboard' }}</title>

    <!-- Dynamic Typography -->
    @php
        $primaryColor = $currentTenant->primary_color ?? '#2563EB';
        $secondaryColor = $currentTenant->secondary_color ?? '#0F172A';
        $typography = $currentTenant->typography ?? 'Inter';
        $googleFontUrl = "https://fonts.googleapis.com/css2?family=" . urlencode($typography) . ":wght@300;400;500;600;700;800&display=swap";
    @endphp

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ $googleFontUrl }}" rel="stylesheet">

    @if(isset($currentTenant) && $currentTenant->favicon_url)
        <link rel="icon" href="{{ $currentTenant->favicon_url }}" type="image/x-icon">
    @endif

    <style>
        :root {
            --primary-color: {{ $primaryColor }};
            --secondary-color: {{ $secondaryColor }};
            --font-family: '{{ $typography }}', sans-serif;
        }
        body {
            font-family: var(--font-family);
        }
        @if(isset($currentTenant) && $currentTenant->email_template)
            @php
                $templateData = json_decode($currentTenant->email_template, true) ?: [];
                $customCss = $templateData['custom_css'] ?? '';
            @endphp
            {!! $customCss !!}
        @endif
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>
<body class="h-full bg-slate-50 text-slate-800 antialiased font-sans flex overflow-hidden">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col shrink-0">
        <div class="h-16 px-6 border-b border-slate-200 flex items-center gap-3">
            @if(isset($currentTenant) && $currentTenant->logo_url)
                <img src="{{ $currentTenant->logo_url }}" alt="{{ $currentTenant->name }}" class="h-6">
            @else
                <span class="text-lg font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    {{ $currentTenant->name ?? 'NANDSKILLS' }}
                </span>
            @endif
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
            @php
                $user = auth()->user();
            @endphp

            @if($user)
                @php
                    $allMods = ['lms', 'crm', 'sis', 'finance', 'tickets', 'meetings', 'hr', 'assets', 'library', 'compliance', 'api'];
                    $activeModules = isset($currentTenant) ? ($currentTenant->active_modules ?? $allMods) : $allMods;
                @endphp

                <!-- Shared Home -->
                <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard Home
                </a>

                <!-- Super Admin Menu -->
                @if($user->hasRole('Super Admin'))
                    <div class="pt-4 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider px-4">Super Admin</div>
                    <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                        Tenants Billing
                    </a>
                    <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                        Plan limits
                    </a>
                @endif

                <!-- Tenant Admin Menu -->
                @if($user->hasRole('Tenant Admin'))
                    <div class="pt-4 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider px-4">Academy Control</div>
                    @if(in_array('lms', $activeModules))
                        <a href="/courses" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                            LMS Courses
                        </a>
                    @endif
                    <a href="/builder" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                        CMS Web Builder
                    </a>
                    @if(in_array('crm', $activeModules))
                        <a href="/leads" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                            CRM Sales
                        </a>
                    @endif
                    @if(in_array('sis', $activeModules))
                        <a href="/students" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                            SIS Profiles
                        </a>
                        <a href="/attendance" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                            SIS Attendance
                        </a>
                    @endif

                    <!-- Academic & Admissions -->
                    <div class="pt-4 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider px-4">Academic & Admissions</div>
                    <a href="/academic/setup" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        ⚙️ Academic Setup
                    </a>
                    <a href="/academic/programs" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        🎓 Programs & Curriculum
                    </a>
                    <a href="/academic/subjects" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        📚 Subject Catalog
                    </a>
                    <a href="/academic/promotions" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        🚀 Student Promotions
                    </a>
                    <a href="/admin/admissions/tracker" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        📋 Admissions Tracker
                    </a>
                    <a href="/admin/admissions/workflow" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        🔗 Intake Builder
                    </a>
                    <a href="/admin/admissions/merit-list" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        🏆 Merit Lists & Offers
                    </a>
                    <a href="/admissions/apply" target="_blank" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        🌐 Public Apply Form
                    </a>
                    <a href="/admin/timetable/builder" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        📅 Timetable Builder
                    </a>
                    <a href="/admin/counseling/cases" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        🩺 Counseling Cases
                    </a>



                    @if(in_array('meetings', $activeModules))
                        <a href="/meetings" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                            Virtual Classrooms
                        </a>
                    @endif
                    @if(in_array('finance', $activeModules))
                        <a href="/billing" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                            ERP Finance
                        </a>
                    @endif
                    @if(in_array('tickets', $activeModules))
                        <a href="/tickets" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                            Helpdesk Tickets
                        </a>
                    @endif
                    
                    <div class="pt-4 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider px-4">Physical & Ops Control</div>
                    <a href="/admin/console?activeTab=campus" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        🏫 Campus Rooms & Buildings
                    </a>
                    @if(in_array('lms', $activeModules))
                        <a href="/admin/console?activeTab=exams" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                            📝 Exams & Transcripts
                        </a>
                    @endif
                    @if(in_array('hr', $activeModules))
                        <a href="/admin/console?activeTab=hr" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                            👥 HR & Payroll System
                        </a>
                    @endif
                    @if(in_array('library', $activeModules))
                        <a href="/admin/console?activeTab=library" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                            📚 Library Inventory
                        </a>
                    @endif
                    @if(in_array('assets', $activeModules))
                        <a href="/admin/console?activeTab=assets" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                            📦 Asset & Device Tracker
                        </a>
                    @endif
                    <a href="/admin/console?activeTab=automations" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        ⚙️ Workflows & Triggers
                    </a>
                    <a href="/admin/console?activeTab=gamification" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        🏆 Gamification & XP
                    </a>
                    @if(in_array('compliance', $activeModules))
                        <a href="/admin/console?activeTab=accreditation" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                            ⚖️ Compliance & Auditing
                        </a>
                    @endif
                    <a href="/admin/console?activeTab=rbac" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        🔐 RBAC Security Gates
                    </a>

                    {{-- ── Enterprise Operations ───────────────────────────────────── --}}
                    <div class="pt-4 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider px-4">Enterprise Operations</div>
                    <a href="/admin/documents" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        🗄️ Document Center
                    </a>
                    <a href="/admin/notifications" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        🔔 Notification Engine
                    </a>
                    <a href="/admin/audit" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        📋 Audit & Activity Log
                    </a>
                    <a href="/admin/audit/security" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        🔐 Security Monitor
                    </a>
                    @if(in_array('compliance', $activeModules))
                    <a href="/admin/compliance" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        🏛️ Accreditation & Compliance
                    </a>
                    @endif
                    <a href="/admin/cms/website" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        🌐 Public Website CMS
                    </a>
                    <a href="/admin/campus/compare" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        🏫 Campus Intelligence
                    </a>
                    <a href="/admin/intelligence" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        🧠 Intelligent Analytics
                    </a>
                    <a href="/parent/dashboard" class="flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-all">
                        👨‍👩‍👧 Parent Portal
                    </a>

                    {{-- ── System Settings ──────────────────────────────────────────── --}}
                    <div class="pt-4 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider px-4">System Settings</div>
                    <a href="/admin/marketplace" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                        🛒 Module Marketplace
                    </a>
                    <a href="/admin/security" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                        🛡️ Security Center
                    </a>
                    <a href="/admin/backup" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                        🗄️ Backup & Recovery
                    </a>
                    <a href="/admin/saas/billing" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                        💳 SaaS Billing
                    </a>
                    <a href="/reports" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                        📊 Reports & Exports
                    </a>
                    @if(in_array('api', $activeModules))
                        <a href="/api/v1/docs" target="_blank" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                            🔌 API Docs
                        </a>
                    @endif
                    <a href="/settings" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                        ⚙️ White-Label Settings
                    </a>
                @endif

                <!-- Trainer Menu -->
                @if($user->hasRole('Trainer'))
                    <div class="pt-4 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider px-4">Trainer Tools</div>
                    @if(in_array('lms', $activeModules))
                        <a href="/classroom" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                            Assigned courses
                        </a>
                    @endif
                    @if(in_array('sis', $activeModules))
                        <a href="/attendance" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                            Log Attendance
                        </a>
                    @endif
                    @if(in_array('meetings', $activeModules))
                        <a href="/meetings" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                            Virtual Classes
                        </a>
                    @endif
                @endif

                <!-- Student Menu -->
                @if($user->hasRole('Student'))
                    <div class="pt-4 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider px-4">My Classroom</div>
                    @if(in_array('lms', $activeModules))
                        <a href="/classroom" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                            My Courses
                        </a>
                    @endif
                    @if(in_array('meetings', $activeModules))
                        <a href="/meetings" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                            Virtual Classes
                        </a>
                    @endif
                    @if(in_array('sis', $activeModules))
                        <a href="/attendance/checkin" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                            Check In Attendance
                        </a>
                    @endif
                    @if(in_array('lms', $activeModules))
                        <a href="/certificates" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                            Certificates
                        </a>
                    @endif
                @endif

                <!-- Parent Menu -->
                <div class="pt-4 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider px-4">Parent Portal</div>
                <a href="/parent/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-all">
                    👨‍👩‍👧 Parent Dashboard
                </a>
            @endif

        </nav>

        <div class="p-4 border-t border-slate-200">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-rose-50 text-sm font-semibold text-rose-600 hover:text-rose-700 transition-all">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Workspace Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Top Navbar -->
        <header class="h-16 border-b border-slate-200 bg-white flex items-center justify-between px-8 shrink-0">
            <h1 class="text-sm font-bold text-slate-800 uppercase tracking-wide">NANDSKILLS Control Panel</h1>

            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-slate-600">{{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}</span>
                <div class="h-8 w-8 rounded-full bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center font-bold text-xs uppercase">
                    {{ substr($user->first_name ?? 'U', 0, 1) }}
                </div>
            </div>
        </header>

        <!-- Dynamic Livewire Sub-content -->
        <main class="flex-1 overflow-y-auto p-8 bg-slate-50/50">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
