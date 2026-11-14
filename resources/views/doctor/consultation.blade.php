<x-layouts.staff>
    <x-slot:title>Ruang Konsultasi - Dokter CampusWell</x-slot:title>
    <x-slot:headerTitle>Ruang Konsultasi Aktif</x-slot:headerTitle>

    <div class="bg-surface rounded-card-sm border border-line p-8 text-center space-y-3">
        <div class="w-14 h-14 rounded-full bg-lavender text-ink flex items-center justify-center mx-auto shadow-card">
            <i data-lucide="messages-square" class="w-6 h-6"></i>
        </div>
        <h3 class="text-base font-semibold text-ink">Belum Ada Sesi Chat Aktif</h3>
        <p class="text-xs text-ink-mute max-w-sm mx-auto">
            Terima permintaan konsultasi dari antrean untuk membuka sesi chat teks dua arah dengan mahasiswa.
        </p>
        <div class="pt-2">
            <x-button variant="primary" href="{{ route('doctor.requests') }}" class="h-10 text-xs">
                <span>Buka Antrean Permintaan</span>
            </x-button>
        </div>
    </div>
</x-layouts.staff>
