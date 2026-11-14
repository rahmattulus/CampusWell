<x-layouts.staff>
    <x-slot:title>Konfigurasi Aturan CDS - Admin CampusWell</x-slot:title>
    <x-slot:headerTitle>Konfigurasi Aturan CDSS (v2.1.0)</x-slot:headerTitle>

    <div class="space-y-4">
        <!-- Status Banner -->
        <div class="p-4 rounded-card-sm bg-status-normal-bg/60 border border-status-normal-fg/20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i data-lucide="shield-check" class="w-5 h-5 text-status-normal-fg"></i>
                <div>
                    <h4 class="text-xs font-semibold text-ink">Versi Aturan Aktif: v2.1.0</h4>
                    <p class="text-[11px] text-ink-mute">Status: Draf Usulan Ahli Keperawatan Klinik Kampus</p>
                </div>
            </div>
            <span class="text-xs font-semibold text-status-normal-fg bg-surface px-3 py-1 rounded-full shadow-xs">
                Tervalidasi
            </span>
        </div>

        <!-- Rules Table -->
        <x-data-table :headers="[
            'ID Aturan',
            'Kelompok',
            'Deskripsi Kondisi',
            'Poin / Aksi',
            'Status'
        ]">
            <tr class="hover:bg-surface-soft transition-colors">
                <td class="px-5 py-3.5 font-mono text-xs font-bold text-ink">F-01</td>
                <td class="px-5 py-3.5 text-xs text-ink-mute">Kelelahan</td>
                <td class="px-5 py-3.5 text-xs text-ink">Durasi tidur &lt; 4.5 jam hari ini</td>
                <td class="px-5 py-3.5 font-semibold text-xs text-ink">+3 Poin</td>
                <td class="px-5 py-3.5 text-xs text-status-normal-fg font-semibold">Aktif</td>
            </tr>
            <tr class="hover:bg-surface-soft transition-colors">
                <td class="px-5 py-3.5 font-mono text-xs font-bold text-ink">F-02</td>
                <td class="px-5 py-3.5 text-xs text-ink-mute">Kelelahan</td>
                <td class="px-5 py-3.5 text-xs text-ink">Streak kurang tidur (tidur &lt; 6j) &ge; 3 hari</td>
                <td class="px-5 py-3.5 font-semibold text-xs text-ink">+2 Poin</td>
                <td class="px-5 py-3.5 text-xs text-status-normal-fg font-semibold">Aktif</td>
            </tr>
            <tr class="hover:bg-surface-soft transition-colors">
                <td class="px-5 py-3.5 font-mono text-xs font-bold text-ink">M-01</td>
                <td class="px-5 py-3.5 text-xs text-ink-mute">Asam Lambung</td>
                <td class="px-5 py-3.5 text-xs text-ink">Frekuensi makan &le; 1 kali sehari</td>
                <td class="px-5 py-3.5 font-semibold text-xs text-ink">+3 Poin</td>
                <td class="px-5 py-3.5 text-xs text-status-normal-fg font-semibold">Aktif</td>
            </tr>
            <tr class="hover:bg-surface-soft transition-colors">
                <td class="px-5 py-3.5 font-mono text-xs font-bold text-ink">M-K1</td>
                <td class="px-5 py-3.5 text-xs text-ink-mute">Asam Lambung</td>
                <td class="px-5 py-3.5 text-xs text-ink">Keluhan perih atau asam naik hari ini</td>
                <td class="px-5 py-3.5 font-semibold text-xs text-ink">Min. Perhatian Mandiri</td>
                <td class="px-5 py-3.5 text-xs text-status-normal-fg font-semibold">Aktif</td>
            </tr>
        </x-data-table>
    </div>
</x-layouts.staff>
