<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-canvas">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'CampusWell') }}</title>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full antialiased font-sans text-ink bg-canvas flex flex-col justify-center items-center py-10 px-4 sm:px-6">
    <div class="w-full max-w-md mx-auto">
        <!-- Logo / Branding -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-ink text-white mb-3 shadow-card">
                <i data-lucide="heart-pulse" class="w-7 h-7 text-sage"></i>
            </div>
            <h1 class="text-2xl font-semibold tracking-tight text-ink">CampusWell</h1>
            <p class="text-sm text-ink-mute mt-1">Platform Pemantauan Kebiasaan Sehat & Telemedisin</p>
        </div>

        <!-- Main Card Slot -->
        <div class="bg-surface rounded-card p-6 sm:p-8 border border-line shadow-card">
            {{ $slot }}
        </div>

        <!-- Footer / Disclaimer -->
        <div class="mt-8 text-center text-xs text-ink-mute">
            <p>Skrining awal kebiasaan sehat, bukan pengganti diagnosis medis.</p>
            <p class="mt-1">&copy; {{ date('Y') }} CampusWell Klinik Kampus.</p>
        </div>
    </div>

    <!-- Toast Notification -->
    <x-toast />
</body>
</html>
