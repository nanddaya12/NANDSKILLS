<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $portalTitle ?? 'NANDSKILLS Portal' }} — {{ isset($currentTenant) ? $currentTenant->name : 'NANDSKILLS' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @php
        $primaryColor = $currentTenant->primary_color ?? '#2563EB';
    @endphp

    <style>
        :root { --primary: {{ $primaryColor }}; }
        body { font-family: 'Inter', sans-serif; }
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
<body class="h-full antialiased bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">

    <!-- Decorative background blobs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
        <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-blue-200/30 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-indigo-200/30 blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full bg-sky-100/20 blur-3xl"></div>
    </div>

    <!-- Main centered card -->
    <div class="relative min-h-full flex flex-col justify-center items-center px-4 py-12">

        <!-- Brand header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center h-16 w-16 rounded-2xl bg-blue-600 text-white text-3xl shadow-xl shadow-blue-600/30 mb-4">
                {{ $portalIcon ?? '🔐' }}
            </div>
            <div class="text-2xl font-extrabold text-slate-800 tracking-tight">
                {{ isset($currentTenant) ? $currentTenant->name : 'NANDSKILLS' }}
            </div>
            <div class="text-sm font-semibold text-blue-600 mt-0.5">{{ $portalTitle ?? 'Portal' }}</div>
        </div>

        <!-- Card -->
        <div class="w-full max-w-md bg-white/90 backdrop-blur-sm border border-white shadow-2xl shadow-slate-200/60 rounded-3xl p-8">
            {{ $slot }}
        </div>

        <!-- Portal switcher links -->
        <div class="mt-6 flex flex-col items-center gap-2 text-xs text-slate-500">
            <div class="flex items-center gap-4">
                @if(!request()->routeIs('student.login'))
                    <a href="{{ route('student.login') }}" class="hover:text-blue-600 transition font-medium">🎓 Student Portal</a>
                @endif
                @if(!request()->is('login'))
                    <a href="{{ url('/login') }}" class="hover:text-blue-600 transition font-medium">🏫 Staff Portal</a>
                @endif
                @if(!request()->routeIs('superadmin.login'))
                    <a href="{{ route('superadmin.login') }}" class="hover:text-slate-700 transition font-medium">🛡️ Admin Portal</a>
                @endif
            </div>
            <span class="text-slate-400">NANDSKILLS &copy; {{ date('Y') }}</span>
        </div>
    </div>

    @livewireScripts
</body>
</html>
