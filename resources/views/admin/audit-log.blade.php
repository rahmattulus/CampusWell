<x-layouts.staff>
    <x-slot:title>Log Audit CDS - Admin CampusWell</x-slot:title>
    <x-slot:headerTitle>Log Audit Skrining CDS & Akses Medis</x-slot:headerTitle>

    <div class="space-y-4">
        <p class="text-xs text-ink-mute">Rekam jejak seluruh evaluasi yang berstatus Perlu Perhatian dan akses rekam medis sesuai prinsip auditabilitas UU PDP.</p>

        <x-data-table :headers="[
            'Waktu',
            'Aksi / Event',
            'Pelaku',
            'Objek',
            'Alamat IP'
        ]">
            <tr class="hover:bg-surface-soft transition-colors">
                <td class="px-5 py-4 text-xs text-ink-mute">Hari ini, 15:40</td>
                <td class="px-5 py-4 font-semibold text-xs text-ink">EVAL_PERLU_PERHATIAN</td>
                <td class="px-5 py-4 text-xs text-ink-soft">Ahmad Fauzi (2201011001)</td>
                <td class="px-5 py-4 text-xs text-status-risk-fg font-medium">Kelelahan Ekstrem & Lambung</td>
                <td class="px-5 py-4 text-xs font-mono text-ink-mute">127.0.0.1</td>
            </tr>
        </x-data-table>
    </div>
</x-layouts.staff>
