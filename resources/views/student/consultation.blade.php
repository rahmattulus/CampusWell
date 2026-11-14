<x-layouts.student>
    <x-slot:title>Konsultasi Dokter - CampusWell</x-slot:title>

    <div class="mb-2">
        <h1 class="text-2xl font-bold text-ink">Konsultasi Dokter</h1>
        <p class="text-xs text-ink-mute mt-0.5">Terhubung langsung dengan tim medis Klinik Kampus.</p>
    </div>

    <!-- Banner Info -->
    <div class="p-3.5 rounded-card-sm bg-lavender/60 border border-lavender text-xs space-y-1">
        <div class="flex items-center gap-2 font-semibold text-ink">
            <i data-lucide="stethoscope" class="w-4 h-4 text-lavender-strong"></i>
            <span>Layanan Tele-konsultasi Teks</span>
        </div>
        <p class="text-ink-soft leading-relaxed">
            Pilih dokter klinik yang berstatus Online untuk konsultasi langsung, atau ajukan pesan asinkron bila dokter sedang Offline.
        </p>
    </div>

    <!-- Doctor List -->
    <div class="space-y-3 pt-2">
        <h3 class="text-sm font-semibold text-ink">Dokter Klinik Tersedia</h3>

        @php
            $doctors = \App\Models\Doctor::with('user')->get();
        @endphp

        @forelse($doctors as $doc)
            <div class="bg-surface p-4 rounded-card border border-line shadow-card space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-full bg-sage flex items-center justify-center font-bold text-sm text-ink shrink-0">
                            {{ strtoupper(substr($doc->user->name ?? 'D', 0, 2)) }}
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-ink">{{ $doc->user->name }}</h4>
                            <p class="text-xs text-ink-mute">{{ $doc->specialization }}</p>
                            <span class="text-[11px] text-ink-soft block mt-0.5">{{ $doc->practice_schedule }}</span>
                        </div>
                    </div>

                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $doc->is_online ? 'bg-status-normal-bg text-status-normal-fg' : 'bg-surface-soft text-ink-mute border border-line' }}">
                        <span class="w-2 h-2 rounded-full {{ $doc->is_online ? 'bg-status-normal-fg animate-pulse' : 'bg-ink-mute' }}"></span>
                        <span>{{ $doc->is_online ? 'Online' : 'Offline' }}</span>
                    </span>
                </div>

                <div class="pt-2 border-t border-line/60 flex justify-end">
                    <x-button variant="{{ $doc->is_online ? 'primary' : 'secondary' }}" class="h-9 px-4 text-xs w-full sm:w-auto">
                        <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                        <span>{{ $doc->is_online ? 'Mulai Konsultasi' : 'Kirim Pesan Asinkron' }}</span>
                    </x-button>
                </div>
            </div>
        @empty
            <x-empty-state icon="stethoscope" title="Belum Ada Dokter" description="Jadwal dokter belum terdaftar di sistem." />
        @endforelse
    </div>
</x-layouts.student>
