<x-layouts.staff>
    <x-slot:title>Permintaan Konsultasi - Dokter CampusWell</x-slot:title>
    <x-slot:headerTitle>Antrean Permintaan Konsultasi</x-slot:headerTitle>

    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <p class="text-xs text-ink-mute">Kelola permintaan konsultasi mahasiswa yang masuk dari evaluasi alert atau menu langsung.</p>
        </div>

        <x-data-table :headers="[
            'Waktu Masuk',
            'Mahasiswa',
            'NIM',
            'Sumber Tiket',
            'Status Skrining',
            'Sisa Waktu',
            ['label' => 'Aksi', 'align' => 'right']
        ]">
            <tr class="hover:bg-surface-soft transition-colors">
                <td class="px-5 py-4 text-xs text-ink-mute">10 menit lalu</td>
                <td class="px-5 py-4 font-semibold text-ink">Ahmad Fauzi</td>
                <td class="px-5 py-4 text-xs font-mono text-ink-soft">2201011001</td>
                <td class="px-5 py-4 text-xs text-ink-mute">Alert CDS</td>
                <td class="px-5 py-4">
                    <x-badge-status level="risk" label="Risiko Kelelahan" size="sm" />
                </td>
                <td class="px-5 py-4 text-xs font-semibold text-status-risk-fg">20 menit</td>
                <td class="px-5 py-4 text-right space-x-2">
                    <x-button variant="primary" class="h-8 px-3 text-xs">
                        Terima
                    </x-button>
                    <x-button variant="secondary" class="h-8 px-3 text-xs">
                        Tolak
                    </x-button>
                </td>
            </tr>
        </x-data-table>
    </div>
</x-layouts.staff>
