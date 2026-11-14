<x-layouts.staff>
    <x-slot:title>Sistem & Keamanan - Admin CampusWell</x-slot:title>
    <x-slot:headerTitle>Pengaturan Sistem & Keamanan</x-slot:headerTitle>

    <div class="max-w-xl space-y-4">
        <x-card tone="surface" :bordered="true" rounded="card-sm" class="space-y-4">
            <div>
                <h3 class="text-sm font-semibold text-ink">Batas Kedaluwarsa Tiket Konsultasi</h3>
                <p class="text-xs text-ink-mute mt-0.5">Sesuai kesepakatan SRS 7.1, tiket kedaluwarsa 30 menit jika dokter online.</p>
            </div>

            <div class="p-3.5 bg-surface-soft rounded-control border border-line flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-ink block">Masa Kedaluwarsa Dokter Online</span>
                    <span class="text-[11px] text-ink-mute">30 menit tanpa respons</span>
                </div>
                <span class="text-xs font-bold text-ink">30 Menit</span>
            </div>
        </x-card>
    </div>
</x-layouts.staff>
