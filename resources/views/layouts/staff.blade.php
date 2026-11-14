<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-canvas">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'CampusWell - Portal Staf Klinik' }}</title>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full antialiased font-sans text-ink bg-canvas flex" x-data="{ sidebarOpen: false }">
    <!-- Sidebar Desktop -->
    <aside class="hidden lg:flex flex-col w-[248px] bg-surface border-r border-line h-screen sticky top-0 shrink-0">
        <!-- Logo Header -->
        <div class="h-16 flex items-center gap-3 px-6 border-b border-line">
            <div class="w-9 h-9 rounded-xl bg-ink text-white flex items-center justify-center shadow-card">
                <i data-lucide="heart-pulse" class="w-5 h-5 text-sage"></i>
            </div>
            <div>
                <h1 class="text-base font-semibold text-ink leading-none">CampusWell</h1>
                <span class="text-[11px] font-medium text-ink-mute uppercase tracking-wider">
                    {{ auth()->user()->isDoctor() ? 'Dokter Klinik' : 'Administrator' }}
                </span>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            @if(auth()->user()->isDoctor())
                <!-- Menu Dokter -->
                <a href="{{ route('doctor.dashboard') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-control text-sm font-medium transition-colors {{ request()->routeIs('doctor.dashboard') ? 'bg-ink text-white' : 'text-ink-soft hover:bg-surface-soft hover:text-ink' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('doctor.requests') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-control text-sm font-medium transition-colors {{ request()->routeIs('doctor.requests*') ? 'bg-ink text-white' : 'text-ink-soft hover:bg-surface-soft hover:text-ink' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="inbox" class="w-4 h-4"></i>
                        <span>Permintaan</span>
                    </div>
                    @php
                        $pendingCount = \App\Models\ConsultTicket::where('status', 'requested')->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="px-2 py-0.5 text-xs rounded-full font-semibold {{ request()->routeIs('doctor.requests*') ? 'bg-white text-ink' : 'bg-status-risk-bg text-status-risk-fg' }}">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('doctor.consultation') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-control text-sm font-medium transition-colors {{ request()->routeIs('doctor.consultation*') ? 'bg-ink text-white' : 'text-ink-soft hover:bg-surface-soft hover:text-ink' }}">
                    <i data-lucide="messages-square" class="w-4 h-4"></i>
                    <span>Ruang Konsultasi</span>
                </a>

                <a href="{{ route('doctor.patients') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-control text-sm font-medium transition-colors {{ request()->routeIs('doctor.patients*') ? 'bg-ink text-white' : 'text-ink-soft hover:bg-surface-soft hover:text-ink' }}">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    <span>Pasien & Rekam Medis</span>
                </a>

                <a href="{{ route('doctor.schedule') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-control text-sm font-medium transition-colors {{ request()->routeIs('doctor.schedule*') ? 'bg-ink text-white' : 'text-ink-soft hover:bg-surface-soft hover:text-ink' }}">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                    <span>Jadwal & Status</span>
                </a>
            @else
                <!-- Menu Admin IT -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-control text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-ink text-white' : 'text-ink-soft hover:bg-surface-soft hover:text-ink' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Dashboard Admin</span>
                </a>

                <a href="{{ route('admin.users') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-control text-sm font-medium transition-colors {{ request()->routeIs('admin.users*') ? 'bg-ink text-white' : 'text-ink-soft hover:bg-surface-soft hover:text-ink' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="user-check" class="w-4 h-4"></i>
                        <span>Manajemen Pengguna</span>
                    </div>
                    @php
                        $pendingUsers = \App\Models\User::where('verification_status', 'pending')->count();
                    @endphp
                    @if($pendingUsers > 0)
                        <span class="px-2 py-0.5 text-xs rounded-full font-semibold {{ request()->routeIs('admin.users*') ? 'bg-white text-ink' : 'bg-status-self-bg text-status-self-fg' }}">
                            {{ $pendingUsers }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.cds-rules') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-control text-sm font-medium transition-colors {{ request()->routeIs('admin.cds-rules*') ? 'bg-ink text-white' : 'text-ink-soft hover:bg-surface-soft hover:text-ink' }}">
                    <i data-lucide="sliders" class="w-4 h-4"></i>
                    <span>Konfigurasi CDS</span>
                </a>

                <a href="{{ route('admin.audit-log') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-control text-sm font-medium transition-colors {{ request()->routeIs('admin.audit-log*') ? 'bg-ink text-white' : 'text-ink-soft hover:bg-surface-soft hover:text-ink' }}">
                    <i data-lucide="shield-alert" class="w-4 h-4"></i>
                    <span>Log Audit CDS</span>
                </a>

                <a href="{{ route('admin.system') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-control text-sm font-medium transition-colors {{ request()->routeIs('admin.system*') ? 'bg-ink text-white' : 'text-ink-soft hover:bg-surface-soft hover:text-ink' }}">
                    <i data-lucide="settings" class="w-4 h-4"></i>
                    <span>Sistem & Keamanan</span>
                </a>
            @endif
        </nav>

        <!-- User Profile Card Bottom -->
        <div class="p-4 border-t border-line">
            <div class="flex items-center justify-between p-2 rounded-control bg-surface-soft">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-sage flex items-center justify-center font-semibold text-xs text-ink shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-semibold text-ink truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-ink-mute truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-1.5 text-ink-mute hover:text-status-risk-fg rounded-md transition-colors" title="Keluar">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto">
        <!-- Topbar Desktop (Height 64px) -->
        <header class="h-16 bg-surface border-b border-line px-6 flex items-center justify-between sticky top-0 z-20 shrink-0">
            <div class="flex items-center gap-4">
                <button type="button" class="lg:hidden p-2 text-ink-soft hover:text-ink" @click="sidebarOpen = true">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <h2 class="text-lg font-semibold text-ink">{{ $headerTitle ?? 'Portal Staf' }}</h2>
            </div>

            <div class="flex items-center gap-4">
                @if(auth()->user()->isDoctor())
                    <!-- Status Online / Offline Toggle Dokter -->
                    @php
                        $doctorProfile = auth()->user()->doctorProfile;
                        $isOnline = $doctorProfile?->is_online ?? false;
                    @endphp
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full border border-line bg-surface text-xs font-medium">
                        <span class="w-2.5 h-2.5 rounded-full {{ $isOnline ? 'bg-status-normal-fg animate-pulse' : 'bg-ink-mute' }}"></span>
                        <span class="{{ $isOnline ? 'text-status-normal-fg font-semibold' : 'text-ink-mute' }}">
                            {{ $isOnline ? 'Online (Menerima Konsultasi)' : 'Offline' }}
                        </span>
                    </div>
                @endif

                <div class="text-xs text-ink-mute">
                    {{ now()->translatedFormat('l, d F Y') }}
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 p-6 lg:p-8 max-w-[1440px] w-full mx-auto space-y-6">
            {{ $slot }}
        </main>
    </div>

    <!-- Toast Notifications -->
    <x-toast />

    @stack('scripts')
</body>
</html>
