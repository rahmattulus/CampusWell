<x-layouts.student>
    <x-slot:title>Beranda - CampusWell Mahasiswa</x-slot:title>

    <!-- Header Greeting -->
    <div class="mb-2">
        <h1 class="text-2xl font-bold text-ink">Kesehatanmu hari ini</h1>
        <p class="text-xs text-ink-mute mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    <!-- Quick Status Welcome Card (Sage) -->
    <x-card tone="sage" class="space-y-4">
        <div class="flex items-center justify-between">
            <x-badge-status level="normal" label="Status: Terjaga Sehat" />
            <span class="text-xs font-semibold text-ink-mute">Skrining Terakhir</span>
        </div>

        <div>
            <span class="text-xs font-medium text-ink-soft block uppercase tracking-wider">Durasi Tidur Semalam</span>
            <div class="flex items-baseline gap-1 mt-1">
                <span class="text-5xl font-medium tracking-tight text-ink font-sans">7.5</span>
                <span class="text-base text-ink-soft font-medium">jam</span>
            </div>
        </div>

        <p class="text-xs text-ink-soft leading-relaxed">
            Pola istirahat dan nutrisimu terpantau seimbang minggu ini. Pertahankan kebiasaan baik ini.
        </p>
    </x-card>

    <!-- Action Card (Butter) -->
    <x-card tone="butter" class="space-y-2">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-ink">Log Harian Kebiasaan</h3>
            <span class="text-[11px] font-semibold text-status-normal-fg bg-status-normal-bg px-2 py-0.5 rounded-full">Hari Ini</span>
        </div>
        <p class="text-xs text-ink-soft">
            Catat durasi tidur, pola makan, stres, dan keluhan harianmu dalam kurang dari 60 detik.
        </p>
        <div class="pt-2">
            <x-button variant="primary" class="w-full text-xs h-11">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Isi Log Sekarang</span>
            </x-button>
        </div>
    </x-card>

    <!-- Logout Button for convenience -->
    <div class="text-center pt-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-xs text-status-risk-fg font-medium hover:underline inline-flex items-center gap-1">
                <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                <span>Keluar dari Akun</span>
            </button>
        </form>
    </div>
</x-layouts.student>
