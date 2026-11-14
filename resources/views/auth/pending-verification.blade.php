<x-layouts.guest>
    <x-slot:title>Menunggu Verifikasi - CampusWell</x-slot:title>

    <div class="space-y-6 text-center">
        <!-- Icon & Badge -->
        <div class="flex flex-col items-center">
            <div class="w-16 h-16 rounded-full bg-status-self-bg text-status-self-fg flex items-center justify-center mb-3 shadow-card ring-4 ring-status-self-bg/30">
                <i data-lucide="clock" class="w-8 h-8"></i>
            </div>
            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-status-self-bg text-status-self-fg border border-status-self-fg/20">
                Status: Menunggu Verifikasi Admin
            </span>
        </div>

        <div>
            <h2 class="text-xl font-bold text-ink">Pendaftaran Berhasil!</h2>
            <p class="text-xs text-ink-soft mt-1.5 leading-relaxed max-w-sm mx-auto">
                Akunmu telah tersimpan di sistem. Administrator klinik kampus sedang memverifikasi NIM dan status kemahasiswaanmu.
            </p>
        </div>

        <!-- Detail Box -->
        <div class="bg-surface-soft rounded-control border border-line p-4 text-left text-xs space-y-2">
            <div class="flex justify-between items-center py-1 border-b border-line/60">
                <span class="text-ink-mute">Nama Lengkap</span>
                <span class="font-semibold text-ink">{{ auth()->user()->name ?? '-' }}</span>
            </div>
            <div class="flex justify-between items-center py-1 border-b border-line/60">
                <span class="text-ink-mute">NIM</span>
                <span class="font-mono font-semibold text-ink">{{ auth()->user()->nim ?? '-' }}</span>
            </div>
            <div class="flex justify-between items-center py-1 border-b border-line/60">
                <span class="text-ink-mute">Email</span>
                <span class="font-semibold text-ink">{{ auth()->user()->email ?? '-' }}</span>
            </div>
            <div class="flex justify-between items-center py-1">
                <span class="text-ink-mute">Tanggal Daftar</span>
                <span class="text-ink">{{ auth()->user()->created_at ? auth()->user()->created_at->translatedFormat('d F Y, H:i') : '-' }}</span>
            </div>
        </div>

        <!-- Info Card -->
        <div class="p-3.5 rounded-control bg-sky/40 border border-sky text-left flex items-start gap-2.5">
            <i data-lucide="info" class="w-4 h-4 text-sky-strong shrink-0 mt-0.5"></i>
            <p class="text-[11px] text-ink leading-relaxed">
                Setelah admin memverifikasi akunmu, kamu dapat langsung mencatat habit harian dan berkonsultasi dengan dokter klinik kampus.
            </p>
        </div>

        <!-- Logout Action -->
        <div class="pt-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-button type="submit" variant="secondary" class="w-full">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span>Keluar dari Sesi</span>
                </x-button>
            </form>
        </div>
    </div>
</x-layouts.guest>
