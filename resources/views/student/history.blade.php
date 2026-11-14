<x-layouts.student>
    <x-slot:title>Riwayat & Grafik - CampusWell</x-slot:title>

    <div class="mb-2">
        <h1 class="text-2xl font-bold text-ink">Riwayat Kebiasaan</h1>
        <p class="text-xs text-ink-mute mt-0.5">Tren tidur, frekuensi makan, dan tingkat stres 7 hari.</p>
    </div>

    <!-- Segmented Tab: Hari / Minggu / Bulan -->
    <div class="flex items-center gap-1.5 p-1 bg-surface-soft rounded-control border border-line">
        <button class="flex-1 h-9 rounded-lg text-xs font-semibold bg-ink text-white shadow-card">7 Hari Terakhir</button>
        <button class="flex-1 h-9 rounded-lg text-xs font-medium text-ink hover:bg-surface">Bulan Ini</button>
    </div>

    <!-- Grafik Preview Card (Sky) -->
    <x-card tone="sky" class="space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-ink uppercase tracking-wider">Tren Durasi Tidur</span>
            <span class="text-xs font-medium text-ink-soft">Rata-rata: 5.7 jam</span>
        </div>
        <div class="h-28 bg-surface/80 rounded-xl p-3 flex items-end justify-between gap-2 border border-line/60">
            @php
                $logs = \App\Models\HabitLog::where('user_id', auth()->id())->latest('date')->take(7)->get()->reverse();
            @endphp
            @forelse($logs as $log)
                <div class="flex-1 flex flex-col items-center gap-1">
                    <span class="text-[10px] font-semibold text-ink">{{ $log->sleep_hours }}j</span>
                    <div class="w-full bg-ink rounded-t-md transition-all" style="height: {{ min(100, ($log->sleep_hours / 10) * 100) }}%;"></div>
                    <span class="text-[9px] text-ink-mute uppercase">{{ \Carbon\Carbon::parse($log->date)->translatedFormat('D') }}</span>
                </div>
            @empty
                <div class="w-full text-center text-xs text-ink-mute self-center">Belum ada data 7 hari.</div>
            @endforelse
        </div>
    </x-card>

    <!-- Riwayat Log List -->
    <div class="space-y-2 pt-2">
        <h3 class="text-sm font-semibold text-ink">Log Tersimpan</h3>
        @forelse($logs ?? [] as $log)
            <div class="bg-surface p-3.5 rounded-card-sm border border-line shadow-card flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-ink block">{{ \Carbon\Carbon::parse($log->date)->translatedFormat('l, d F Y') }}</span>
                    <span class="text-[11px] text-ink-mute">Tidur {{ $log->sleep_hours }}j · Makan {{ $log->meal_freq }}x · Stres {{ $log->stress_level }}/5</span>
                </div>
                <x-badge-status level="{{ $log->sleep_hours < 5 ? 'risk' : ($log->sleep_hours < 7 ? 'self' : 'normal') }}" size="sm" />
            </div>
        @empty
            <x-empty-state icon="activity" title="Belum Ada Log" description="Kamu belum mencatat habit harian." />
        @endforelse
    </div>
</x-layouts.student>
