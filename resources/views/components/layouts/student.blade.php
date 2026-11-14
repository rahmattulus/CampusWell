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
<body class="min-h-full antialiased font-sans text-ink bg-canvas selection:bg-sage selection:text-ink md:bg-surface-soft/60 flex flex-col items-center justify-start md:py-6">

    <!-- Desktop Companion Header (Hidden on Mobile) -->
    <header class="hidden md:flex items-center justify-between w-full max-w-[520px] mb-3 px-2 text-xs text-ink-mute">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-lg bg-ink text-white flex items-center justify-center shadow-xs">
                <i data-lucide="heart-pulse" class="w-3.5 h-3.5 text-sage"></i>
            </div>
            <span class="font-bold text-ink">CampusWell Mobile</span>
            <span class="px-2 py-0.5 rounded-full bg-surface border border-line text-[10px] font-semibold text-ink-soft">Mahasiswa</span>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-ink-soft font-medium">{{ auth()->user()->name ?? 'Mahasiswa' }}</span>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-ink-mute hover:text-status-risk-fg transition-colors" title="Keluar">
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                </button>
            </form>
        </div>
    </header>

    <!-- Mobile Shell / Responsive Device Container -->
    <div class="w-full max-w-[480px] min-h-screen md:min-h-[820px] md:max-h-[90vh] bg-canvas md:rounded-[36px] md:border md:border-line md:shadow-float flex flex-col relative overflow-hidden transition-all">

        <!-- Mobile Top Speaker/Notch (Desktop only decoration) -->
        <div class="hidden md:flex justify-center pt-2.5 pb-1 shrink-0 bg-canvas">
            <div class="w-20 h-1 bg-line rounded-full"></div>
        </div>

        <!-- Sticky App Bar Header inside Mobile Shell -->
        <header class="px-4 py-3 shrink-0 flex items-center justify-between border-b border-line/40 bg-canvas/90 backdrop-blur-md sticky top-0 z-20">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('student.profile') }}" class="w-10 h-10 rounded-full bg-sage flex items-center justify-center text-ink font-bold text-sm shadow-card ring-2 ring-white shrink-0 hover:scale-105 transition-transform">
                    {{ strtoupper(substr(auth()->user()->name ?? 'M', 0, 2)) }}
                </a>
                <div class="truncate">
                    <span class="text-[11px] text-ink-mute block font-medium leading-none mb-0.5">Klinik Kampus</span>
                    <h2 class="text-sm font-bold text-ink leading-tight truncate">{{ auth()->user()->name ?? 'Mahasiswa' }}</h2>
                </div>
            </div>

            <div class="flex items-center gap-1.5 shrink-0">
                @if(isset($headerActions))
                    {{ $headerActions }}
                @else
                    <a href="{{ route('student.profile') }}" class="w-9 h-9 rounded-full bg-surface border border-line flex items-center justify-center text-ink shadow-card hover:bg-surface-soft active:scale-95 transition-all" title="Profil">
                        <i data-lucide="bell" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </header>

        <!-- Scrollable Main Content -->
        <main class="flex-1 overflow-y-auto px-4 pt-3 pb-24 space-y-3">
            {{ $slot }}
        </main>

        <!-- Floating Bottom Navigation -->
        <!-- Locked to bottom of mobile shell both on native mobile (fixed) and desktop (absolute inside relative shell) -->
        <nav class="fixed md:absolute bottom-3 left-1/2 -translate-x-1/2 w-[calc(100%-2rem)] max-w-[420px] bg-lavender/95 backdrop-blur-md rounded-full p-1.5 shadow-float border border-white/80 z-30">
            <div class="flex items-center justify-around">
                <!-- 1. Beranda -->
                <a href="{{ route('student.home') }}" 
                   class="flex items-center justify-center transition-all active:scale-90 {{ request()->routeIs('student.home') ? 'w-11 h-11 bg-ink text-white rounded-full shadow-card' : 'w-11 h-11 text-ink-soft hover:text-ink hover:bg-white/40 rounded-full' }}"
                   title="Beranda">
                    <i data-lucide="home" class="w-5 h-5"></i>
                </a>

                <!-- 2. Riwayat -->
                <a href="{{ route('student.history') }}" 
                   class="flex items-center justify-center transition-all active:scale-90 {{ request()->routeIs('student.history*') ? 'w-11 h-11 bg-ink text-white rounded-full shadow-card' : 'w-11 h-11 text-ink-soft hover:text-ink hover:bg-white/40 rounded-full' }}"
                   title="Riwayat">
                    <i data-lucide="activity" class="w-5 h-5"></i>
                </a>

                <!-- 3. Konsultasi -->
                <a href="{{ route('student.consultation') }}" 
                   class="flex items-center justify-center transition-all active:scale-90 {{ request()->routeIs('student.consultation*') ? 'w-11 h-11 bg-ink text-white rounded-full shadow-card' : 'w-11 h-11 text-ink-soft hover:text-ink hover:bg-white/40 rounded-full' }}"
                   title="Konsultasi">
                    <i data-lucide="message-square" class="w-5 h-5"></i>
                </a>

                <!-- 4. Profil -->
                <a href="{{ route('student.profile') }}" 
                   class="flex items-center justify-center transition-all active:scale-90 {{ request()->routeIs('student.profile*') ? 'w-11 h-11 bg-ink text-white rounded-full shadow-card' : 'w-11 h-11 text-ink-soft hover:text-ink hover:bg-white/40 rounded-full' }}"
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
