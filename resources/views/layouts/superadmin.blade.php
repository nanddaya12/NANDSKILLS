<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>{{ $pageTitle ?? 'Secure Admin' }} — NANDSKILLS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Subtle noise texture for security feel */
        .admin-bg {
            background-color: #0f172a;
            background-image:
                radial-gradient(ellipse at 20% 20%, rgba(30, 58, 138, 0.25) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 80%, rgba(15, 23, 42, 0.8) 0%, transparent 60%);
        }
        .admin-card {
            background: rgba(15, 23, 42, 0.95);
            border: 1px solid rgba(99, 102, 241, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.8), 0 0 0 1px rgba(99,102,241,0.1);
            backdrop-filter: blur(20px);
        }
        .admin-input {
            background: rgba(30, 41, 59, 0.8) !important;
            border-color: rgba(71, 85, 105, 0.6) !important;
            color: #e2e8f0 !important;
        }
        .admin-input:focus {
            border-color: rgba(99, 102, 241, 0.8) !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
        }
        .admin-input::placeholder { color: #475569 !important; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

{{-- Deep dark isolated background — deliberately different from all public pages --}}
<body class="h-full admin-bg antialiased">

    {{-- Security header strip --}}
    <div class="fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-6 py-2 bg-slate-950/80 border-b border-indigo-900/40 backdrop-blur-sm">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <svg class="h-3 w-3 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
            </svg>
            <span>Secure Admin Portal — All access is monitored and logged</span>
        </div>
        <div class="text-xs text-slate-600 font-mono">NANDSKILLS v1.0 &bull; {{ request()->ip() }}</div>
    </div>

    {{-- Centered content --}}
    <div class="min-h-full flex flex-col items-center justify-center px-4 py-16 pt-20">

        {{-- Brand lockup --}}
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center h-14 w-14 rounded-2xl bg-indigo-600 shadow-xl shadow-indigo-900/50 mb-4 border border-indigo-500/50">
                <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div class="text-lg font-bold text-white tracking-tight">NANDSKILLS</div>
            <div class="text-xs font-semibold text-indigo-400 tracking-widest uppercase mt-0.5">
                {{ $pageTitle ?? 'Administration Portal' }}
            </div>
        </div>

        {{-- Card --}}
        <div class="w-full max-w-md admin-card rounded-2xl p-8">
            {{ $slot }}
        </div>

        {{-- IMPORTANT: No links to public site from admin area --}}
        <p class="mt-6 text-xs text-slate-600 text-center">
            This is a restricted internal system. Unauthorized access is prohibited.
        </p>
    </div>

    @livewireScripts
</body>
</html>
