<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($currentTenant) ? $currentTenant->name : 'NANDSKILLS' }}</title>

    <!-- Dynamic Typography from Tenant Config -->
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

    <!-- Dynamic Theme Styling Injection -->
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

    <!-- Vite Assets (Includes Tailwind CSS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>
<body class="h-full bg-slate-50 text-slate-900 antialiased">
    <!-- Main content rendering -->
    {{ $slot }}

    @livewireScripts
</body>
</html>
