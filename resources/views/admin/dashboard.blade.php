<x-layouts.staff>
    <x-slot:title>Dashboard Admin IT - CampusWell</x-slot:title>
    <x-slot:headerTitle>Dashboard Administrasi Sistem</x-slot:headerTitle>

    <!-- Top 4 Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-card tone="surface" :bordered="true" rounded="card-sm">
            <span class="text-xs font-semibold text-ink-mute uppercase tracking-wider block">Total Pengguna</span>
            <div class="text-3xl font-bold text-ink mt-2">5</div>
            <p class="text-[11px] text-ink-mute mt-1">Mahasiswa & Dokter</p>
        </x-card>

        <x-card tone="surface" :bordered="true" rounded="card-sm" class="border-l-4 border-l-status-self-fg">
            <span class="text-xs font-semibold text-status-self-fg uppercase tracking-wider block">Menunggu Verifikasi</span>
            <div class="text-3xl font-bold text-status-self-fg mt-2">1</div>
            <p class="text-[11px] text-ink-mute mt-1">Akun mahasiswa baru</p>
        </x-card>

        <x-card tone="surface" :bordered="true" rounded="card-sm">
            <span class="text-xs font-semibold text-ink-mute uppercase tracking-wider block">Evaluasi CDS Hari Ini</span>
            <div class="text-3xl font-bold text-ink mt-2">7</div>
            <p class="text-[11px] text-ink-mute mt-1">Habit logs terproses</p>
        </x-card>

        <x-card tone="surface" :bordered="true" rounded="card-sm">
            <span class="text-xs font-semibold text-ink-mute uppercase tracking-wider block">Versi Aturan CDS</span>
            <div class="text-3xl font-bold text-ink mt-2">v2.1.0</div>
            <p class="text-[11px] text-status-normal-fg font-semibold mt-1">Aktif & Tervalidasi</p>
        </x-card>
    </div>

    <!-- Quick User Table -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-semibold text-ink">Verifikasi Akun Mahasiswa Baru</h3>
            <span class="text-xs text-ink-mute">Aksi diperlukan</span>
        </div>

        <x-data-table :headers="[
            'Nama Lengkap',
            'NIM',
            'Email',
            'Status',
            ['label' => 'Aksi', 'align' => 'right']
        ]">
            <tr class="hover:bg-surface-soft transition-colors">
                <td class="px-5 py-4 font-semibold text-ink">Siti Rahma</td>
                <td class="px-5 py-4 text-xs font-mono text-ink-soft">2201011002</td>
                <td class="px-5 py-4 text-xs text-ink-mute">siti.rahma@student.ac.id</td>
                <td class="px-5 py-4">
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-status-self-bg text-status-self-fg">
                        Menunggu Verifikasi
                    </span>
                </td>
                <td class="px-5 py-4 text-right space-x-1.5">
                    <x-button variant="primary" class="h-8 px-3 text-xs">
                        Setujui
                    </x-button>
                    <x-button variant="danger" class="h-8 px-3 text-xs">
                        Tolak
                    </x-button>
                </td>
            </tr>
        </x-data-table>
    </div>
</x-layouts.staff>
