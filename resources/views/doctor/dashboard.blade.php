<x-layouts.staff>
    <x-slot:title>Dashboard Dokter - CampusWell</x-slot:title>
    <x-slot:headerTitle>Dashboard Dokter Klinik</x-slot:headerTitle>

    <!-- Top 4 Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-card tone="surface" :bordered="true" rounded="card-sm">
            <span class="text-xs font-semibold text-ink-mute uppercase tracking-wider block">Permintaan Menunggu</span>
            <div class="text-3xl font-bold text-ink mt-2">1</div>
            <p class="text-[11px] text-ink-mute mt-1">Sesi baru siap diterima</p>
        </x-card>

        <x-card tone="surface" :bordered="true" rounded="card-sm">
            <span class="text-xs font-semibold text-ink-mute uppercase tracking-wider block">Konsultasi Aktif</span>
            <div class="text-3xl font-bold text-ink mt-2">0</div>
            <p class="text-[11px] text-ink-mute mt-1">Sesi chat sedang berjalan</p>
        </x-card>

        <x-card tone="surface" :bordered="true" rounded="card-sm">
            <span class="text-xs font-semibold text-ink-mute uppercase tracking-wider block">Selesai Hari Ini</span>
            <div class="text-3xl font-bold text-ink mt-2">0</div>
            <p class="text-[11px] text-ink-mute mt-1">Rekam medis tersimpan</p>
        </x-card>

        <x-card tone="surface" :bordered="true" rounded="card-sm" class="border-l-4 border-l-status-risk-fg">
            <span class="text-xs font-semibold text-status-risk-fg uppercase tracking-wider block">Pasien Perlu Perhatian</span>
            <div class="text-3xl font-bold text-status-risk-fg mt-2">1</div>
            <p class="text-[11px] text-ink-mute mt-1">Kelelahan / Asam Lambung</p>
        </x-card>
    </div>

    <!-- Antrean Permintaan Tabel -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-semibold text-ink">Antrean Permintaan Konsultasi Masuk</h3>
            <span class="text-xs text-ink-mute">Diperbarui otomatis</span>
        </div>

        <x-data-table :headers="[
            'Waktu Masuk',
            'Nama Mahasiswa',
            'NIM',
            'Status Skrining CDS',
            'Sisa Waktu',
            ['label' => 'Aksi', 'align' => 'right']
        ]">
            <tr class="hover:bg-surface-soft transition-colors">
                <td class="px-5 py-4 text-xs text-ink-mute">10 menit lalu</td>
                <td class="px-5 py-4 font-semibold text-ink">Ahmad Fauzi</td>
                <td class="px-5 py-4 text-xs font-mono text-ink-soft">2201011001</td>
                <td class="px-5 py-4">
                    <x-badge-status level="risk" label="Risiko Kelelahan" size="sm" />
                </td>
                <td class="px-5 py-4 text-xs font-semibold text-status-risk-fg">20 menit</td>
                <td class="px-5 py-4 text-right">
                    <x-button variant="primary" class="h-9 px-4 text-xs">
                        <span>Terima Sesi</span>
                    </x-button>
                </td>
            </tr>
        </x-data-table>
    </div>
</x-layouts.staff>
