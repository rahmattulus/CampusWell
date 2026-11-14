<x-layouts.staff>
    <x-slot:title>Manajemen Pengguna - Admin CampusWell</x-slot:title>
    <x-slot:headerTitle>Manajemen Pengguna & Verifikasi NIM</x-slot:headerTitle>

    <div class="space-y-4">
        <!-- Tabs Mahasiswa / Dokter / Admin -->
        <div class="flex items-center gap-2 border-b border-line pb-2">
            <button class="px-3.5 py-1.5 text-xs font-semibold rounded-full bg-ink text-white">Mahasiswa (3)</button>
            <button class="px-3.5 py-1.5 text-xs font-medium text-ink-mute hover:text-ink">Dokter (2)</button>
            <button class="px-3.5 py-1.5 text-xs font-medium text-ink-mute hover:text-ink">Admin (1)</button>
        </div>

        @php
            $users = \App\Models\User::where('role', 'student')->latest()->get();
        @endphp

        <x-data-table :headers="[
            'Nama Mahasiswa',
            'NIM',
            'Email',
            'Status Akun',
            ['label' => 'Aksi', 'align' => 'right']
        ]">
            @foreach($users as $u)
                <tr class="hover:bg-surface-soft transition-colors">
                    <td class="px-5 py-4 font-semibold text-ink">{{ $u->name }}</td>
                    <td class="px-5 py-4 text-xs font-mono text-ink-soft">{{ $u->nim ?? '-' }}</td>
                    <td class="px-5 py-4 text-xs text-ink-mute">{{ $u->email }}</td>
                    <td class="px-5 py-4">
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $u->verification_status === 'verified' ? 'bg-status-normal-bg text-status-normal-fg' : 'bg-status-self-bg text-status-self-fg' }}">
                            {{ $u->verification_status === 'verified' ? 'Terverifikasi' : 'Menunggu Verifikasi' }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-right space-x-1">
                        @if($u->verification_status === 'pending')
                            <x-button variant="primary" class="h-8 px-3 text-xs">Setujui</x-button>
                            <x-button variant="danger" class="h-8 px-3 text-xs">Tolak</x-button>
                        @else
                            <x-button variant="secondary" class="h-8 px-3 text-xs">Detail</x-button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </x-data-table>
    </div>
</x-layouts.staff>
