<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $pageTitle ?? 'Staff Login' }}{{ isset($currentTenant) ? ' — ' . $currentTenant->name : '' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @php
        $accent = $currentTenant->primary_color ?? '#2563EB';
    @endphp

    <style>
        body { font-family: 'Inter', sans-serif; }
        :root { --accent: {{ $accent }}; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

{{-- Clean white/light background — separate from public CMS pages --}}
<body class="h-full bg-slate-100 antialiased">

    {{-- Staff-only top bar — no public navigation --}}
    <div class="fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-6 py-2.5 bg-white border-b border-slate-200 shadow-sm">
        <div class="flex items-center gap-2.5">
            @if(isset($currentTenant) && $currentTenant->logo_url)
                <img src="{{ $currentTenant->logo_url }}" class="h-6" alt="">
            @else
                <div class="h-6 w-6 rounded bg-blue-600 flex items-center justify-center">
                    <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            @endif
            <span class="font-bold text-slate-800 text-sm">
                {{ isset($currentTenant) ? $currentTenant->name : 'NANDSKILLS' }}
            </span>
            <span class="text-slate-300">|</span>
            <span class="text-xs font-medium text-slate-500">Staff Portal</span>
        </div>
        {{-- No public site link here — staff portal is isolated --}}
        <span class="text-xs text-slate-400">Internal Access Only</span>
    </div>

    {{-- Centered login card --}}
    <div class="min-h-full flex flex-col items-center justify-center px-4 py-16 pt-20">

        <div class="mb-7 text-center">
            <div class="inline-flex items-center justify-center h-12 w-12 rounded-xl bg-blue-600 shadow-lg shadow-blue-600/30 mb-3">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div class="font-bold text-slate-800 text-lg">Staff Sign In</div>
            <div class="text-xs text-slate-500 mt-0.5">
                {{ isset($currentTenant) ? $currentTenant->name : 'NANDSKILLS' }} — Staff &amp; Administrators
            </div>
        </div>

        <div class="w-full max-w-md bg-white border border-slate-200 rounded-2xl shadow-lg p-8">
            {{ $slot }}
        </div>

        {{-- No links to public site from staff portal --}}
        <p class="mt-5 text-xs text-slate-400 text-center">
            This portal is restricted to authorised staff only.
        </p>
    </div>

    @livewireScripts
</body>
</html>
