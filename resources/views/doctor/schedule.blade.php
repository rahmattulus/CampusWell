<x-layouts.staff>
    <x-slot:title>Jadwal & Status - Dokter CampusWell</x-slot:title>
    <x-slot:headerTitle>Pengaturan Jadwal & Status Dokter</x-slot:headerTitle>

    <div class="max-w-xl space-y-4">
        <x-card tone="surface" :bordered="true" rounded="card-sm" class="space-y-4">
            <div>
                <h3 class="text-sm font-semibold text-ink">Status Online / Offline</h3>
                <p class="text-xs text-ink-mute mt-0.5">Saat Online, mahasiswa dapat melihat ketersediaanmu dan memulai telekonsultasi langsung.</p>
            </div>

            @php
                $doctorProfile = auth()->user()->doctorProfile;
            @endphp

            <div class="p-4 rounded-control bg-surface-soft border border-line flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-3.5 h-3.5 rounded-full {{ $doctorProfile?->is_online ? 'bg-status-normal-fg animate-pulse' : 'bg-ink-mute' }}"></span>
                    <div>
                        <span class="text-sm font-semibold text-ink block">{{ $doctorProfile?->is_online ? 'Sedang Online' : 'Sedang Offline' }}</span>
                        <span class="text-xs text-ink-mute">{{ $doctorProfile?->practice_schedule ?? 'Jadwal praktik standar' }}</span>
                    </div>
                </div>

                <x-button variant="primary" class="h-9 px-4 text-xs">
                    <span>Ubah Status</span>
                </x-button>
            </div>
        </x-card>
    </div>
</x-layouts.staff>
