<x-layouts.student>
    <x-slot:title>Profil Saya - CampusWell</x-slot:title>

    <div class="mb-2">
        <h1 class="text-2xl font-bold text-ink">Profil & Pengaturan</h1>
        <p class="text-xs text-ink-mute mt-0.5">Informasi akun dan profil faktor risiko kesehatan.</p>
    </div>

    <!-- Student Info Card -->
    <x-card tone="surface" :bordered="true" class="space-y-3">
        <div class="flex items-center gap-3 pb-3 border-b border-line">
            <div class="w-12 h-12 rounded-full bg-sage flex items-center justify-center font-bold text-base text-ink shadow-card">
                {{ strtoupper(substr(auth()->user()->name ?? 'M', 0, 2)) }}
            </div>
            <div>
                <h3 class="text-base font-semibold text-ink">{{ auth()->user()->name }}</h3>
                <p class="text-xs font-mono text-ink-mute">NIM: {{ auth()->user()->nim }}</p>
                <span class="inline-block px-2 py-0.5 mt-1 text-[10px] font-semibold rounded-full bg-status-normal-bg text-status-normal-fg">
                    Terverifikasi
                </span>
            </div>
        </div>

        <div class="text-xs space-y-2">
            <div class="flex justify-between py-1 border-b border-line/60">
                <span class="text-ink-mute">Email Kampus</span>
                <span class="font-medium text-ink">{{ auth()->user()->email }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-line/60">
                <span class="text-ink-mute">Riwayat Maag/GERD</span>
                <span class="font-semibold text-ink">{{ auth()->user()->profile->p1_gastric_history ?? '-' }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-line/60">
                <span class="text-ink-mute">Kebiasaan Merokok</span>
                <span class="font-semibold text-ink">{{ auth()->user()->profile->p2_smoking ?? '-' }}</span>
            </div>
            <div class="flex justify-between py-1">
                <span class="text-ink-mute">Pemakaian NSAID</span>
                <span class="font-semibold text-ink">{{ auth()->user()->profile->p3_nsaid_usage ?? '-' }}</span>
            </div>
        </div>
    </x-card>

    <!-- Logout Button -->
    <div class="pt-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-button type="submit" variant="danger" class="w-full">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                <span>Keluar dari Akun</span>
            </x-button>
        </form>
    </div>
</x-layouts.student>
