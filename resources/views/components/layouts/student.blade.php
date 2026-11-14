<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-canvas">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'CampusWell - Mahasiswa' }}</title>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full antialiased font-sans text-ink bg-canvas selection:bg-sage selection:text-ink">
    <div class="max-w-[480px] mx-auto min-h-screen flex flex-col px-4 pt-4 pb-28 relative">
        <!-- Top App Bar -->
        <header class="flex items-center justify-between py-3 mb-2">
            <div class="flex items-center gap-3">
                <a href="{{ route('student.profile') }}" class="w-10 h-10 rounded-full bg-sage flex items-center justify-center text-ink font-semibold text-sm shadow-card ring-2 ring-white">
                    {{ strtoupper(substr(auth()->user()->name ?? 'M', 0, 2)) }}
                </a>
                <div>
                    <span class="text-xs text-ink-mute block font-medium">Klinik Kampus</span>
                    <h2 class="text-base font-semibold text-ink leading-tight">{{ auth()->user()->name ?? 'Mahasiswa' }}</h2>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if(isset($headerActions))
                    {{ $headerActions }}
                @else
                    <button type="button" class="w-10 h-10 rounded-full bg-surface border border-line flex items-center justify-center text-ink shadow-card hover:bg-surface-soft active:scale-95 transition-all">
                        <i data-lucide="bell" class="w-4 h-4"></i>
                    </button>
                @endif
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 space-y-3">
            {{ $slot }}
        </main>

        <!-- Floating Bottom Navigation -->
        <nav class="fixed bottom-4 left-1/2 -translate-x-1/2 w-[calc(100%-2rem)] max-w-[448px] bg-lavender/95 backdrop-blur-md rounded-full p-2 shadow-float border border-white/60 z-30">
            <div class="flex items-center justify-around">
                <!-- 1. Beranda -->
                <a href="{{ route('student.home') }}" 
                   class="flex items-center justify-center transition-transform active:scale-90 {{ request()->routeIs('student.home') ? 'w-11 h-11 bg-ink text-white rounded-full shadow-card' : 'w-11 h-11 text-ink-soft hover:text-ink' }}"
                   title="Beranda">
                    <i data-lucide="home" class="w-5 h-5"></i>
                </a>

                <!-- 2. Riwayat -->
                <a href="{{ route('student.history') }}" 
                   class="flex items-center justify-center transition-transform active:scale-90 {{ request()->routeIs('student.history*') ? 'w-11 h-11 bg-ink text-white rounded-full shadow-card' : 'w-11 h-11 text-ink-soft hover:text-ink' }}"
                   title="Riwayat">
                    <i data-lucide="activity" class="w-5 h-5"></i>
                </a>

                <!-- 3. Konsultasi -->
                <a href="{{ route('student.consultation') }}" 
                   class="flex items-center justify-center transition-transform active:scale-90 {{ request()->routeIs('student.consultation*') ? 'w-11 h-11 bg-ink text-white rounded-full shadow-card' : 'w-11 h-11 text-ink-soft hover:text-ink' }}"
                   title="Konsultasi">
                    <i data-lucide="message-square" class="w-5 h-5"></i>
                </a>

                <!-- 4. Profil -->
                <a href="{{ route('student.profile') }}" 
                   class="flex items-center justify-center transition-transform active:scale-90 {{ request()->routeIs('student.profile*') ? 'w-11 h-11 bg-ink text-white rounded-full shadow-card' : 'w-11 h-11 text-ink-soft hover:text-ink' }}"
                   title="Profil">
                    <i data-lucide="user" class="w-5 h-5"></i>
                </a>
            </div>
        </nav>
    </div>

    <!-- Toast Notifications -->
    <x-toast />

    @stack('scripts')
</body>
</html>
