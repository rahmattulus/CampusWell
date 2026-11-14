<x-layouts.guest>
    <x-slot:title>Masuk - CampusWell</x-slot:title>

    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-bold text-ink">Masuk ke Akun</h2>
            <p class="text-xs text-ink-mute mt-1">Gunakan email dan kata sandimu untuk mengakses layanan klinik.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Field -->
            <x-form-field 
                name="email" 
                label="Alamat Email" 
                type="email" 
                :value="old('email')"
                placeholder="nama@student.ac.id / dokter / admin" 
                required 
                autofocus
            />

            <!-- Password Field -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-xs font-semibold text-ink uppercase tracking-wider">
                        Kata Sandi <span class="text-status-risk-fg">*</span>
                    </label>
                    <a href="#" class="text-xs text-ink-mute hover:text-ink font-medium transition-colors">
                        Lupa sandi?
                    </a>
                </div>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    placeholder="••••••••" 
                    required
                    class="w-full h-12 px-4 rounded-control bg-surface border border-line text-sm text-ink placeholder:text-ink-mute focus:outline-none focus:ring-2 focus:ring-ink focus:border-transparent transition-all"
                >
                @error('password')
                    <p class="text-xs font-medium text-status-risk-fg flex items-center gap-1 mt-1">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <!-- Remember Me Checkbox -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input 
                        type="checkbox" 
                        name="remember" 
                        class="w-4 h-4 rounded border-line text-ink focus:ring-ink cursor-pointer accent-ink"
                    >
                    <span class="text-xs text-ink-soft font-medium">Ingat saya di perangkat ini</span>
                </label>
            </div>

            <!-- Submit Button (Primer Hitam Pekat) -->
            <x-button type="submit" variant="primary" class="w-full mt-2">
                <span>Masuk Sekarang</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </x-button>
        </form>

        <!-- Akun Demo Cepat (Quick Demo Fill) -->
        <div class="pt-4 border-t border-line" x-data="{
            fill(email, pass) {
                document.getElementById('email').value = email;
                document.getElementById('password').value = pass;
            }
        }">
            <p class="text-[11px] font-semibold text-ink-mute uppercase tracking-wider mb-2 text-center">Akun Uji Coba Cepat (Demo):</p>
            <div class="grid grid-cols-3 gap-1.5 text-center">
                <button type="button" @click="fill('ahmad.fauzi@student.ac.id', 'password')" 
                    class="p-2 rounded-lg bg-surface-soft border border-line text-[11px] text-ink font-semibold hover:bg-sage transition-colors">
                    Mahasiswa
                </button>
                <button type="button" @click="fill('dr.sarah@campuswell.ac.id', 'password')" 
                    class="p-2 rounded-lg bg-surface-soft border border-line text-[11px] text-ink font-semibold hover:bg-lavender transition-colors">
                    Dokter
                </button>
                <button type="button" @click="fill('admin@campuswell.ac.id', 'password')" 
                    class="p-2 rounded-lg bg-surface-soft border border-line text-[11px] text-ink font-semibold hover:bg-sky transition-colors">
                    Admin IT
                </button>
            </div>
        </div>

        <!-- Registration & Staff Links -->
        <div class="text-center pt-2 text-xs text-ink-mute space-y-2">
            <div>
                <span>Belum memiliki akun?</span>
                <a href="{{ route('register') }}" class="font-semibold text-ink hover:underline ml-1">Daftar sebagai Mahasiswa</a>
            </div>
            <div>
                <a href="{{ route('login.staff') }}" class="text-ink-mute hover:text-ink font-medium transition-colors">
                    Masuk sebagai Dokter / Staf Klinik &rarr;
                </a>
            </div>
        </div>
    </div>
</x-layouts.guest>
