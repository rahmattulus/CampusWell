<x-layouts.staff>
    <x-slot:title>Pasien & Rekam Medis - Dokter CampusWell</x-slot:title>
    <x-slot:headerTitle>Pasien & Rekam Medis</x-slot:headerTitle>

    <div class="space-y-4">
        <x-data-table :headers="[
            'Nama Pasien',
            'NIM',
            'Total Sesi',
            'Konsultasi Terakhir',
            ['label' => 'Aksi', 'align' => 'right']
        ]">
            <tr class="hover:bg-surface-soft transition-colors">
                <td class="px-5 py-4 font-semibold text-ink">Ahmad Fauzi</td>
                <td class="px-5 py-4 text-xs font-mono text-ink-mute">2201011001</td>
                <td class="px-5 py-4 text-xs text-ink-soft">1 Sesi</td>
                <td class="px-5 py-4 text-xs text-ink-mute">Hari ini</td>
                <td class="px-5 py-4 text-right">
                    <x-button variant="secondary" class="h-8 px-3 text-xs">
                        Lihat Rekam Medis
                    </x-button>
                </td>
            </tr>
        </x-data-table>
    </div>
</x-layouts.staff>
