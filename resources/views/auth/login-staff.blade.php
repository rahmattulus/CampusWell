<x-layouts.guest>
    <x-slot:title>Masuk Staf Klinik - CampusWell</x-slot:title>

    <div class="space-y-6">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-surface-soft border border-line text-xs font-semibold text-ink-mute mb-2">
                <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                <span>Portal Dokter & Administrator</span>
            </div>
            <h2 class="text-xl font-bold text-ink">Masuk Staf Klinik</h2>
            <p class="text-xs text-ink-mute mt-1">Gunakan akun staf resmi untuk mengelola antrean dan konsultasi.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Field -->
            <x-form-field 
                name="email" 
                label="Email Staf" 
                type="email" 
                :value="old('email')"
                placeholder="dr.sarah@campuswell.ac.id / admin" 
                required 
                autofocus
            />

            <!-- Password Field -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-xs font-semibold text-ink uppercase tracking-wider">
                        Kata Sandi <span class="text-status-risk-fg">*</span>
                    </label>
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
                    <p class="text-xs font-medium text-status-risk-fg mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <x-button type="submit" variant="primary" class="w-full mt-2">
                <span>Masuk ke Portal Staf</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </x-button>
        </form>

        <!-- Quick Demo Fill -->
        <div class="pt-4 border-t border-line" x-data="{
            fill(email, pass) {
                document.getElementById('email').value = email;
                document.getElementById('password').value = pass;
            }
        }">
            <p class="text-[11px] font-semibold text-ink-mute uppercase tracking-wider mb-2 text-center">Akun Demo Staf:</p>
            <div class="grid grid-cols-2 gap-2 text-center">
                <button type="button" @click="fill('dr.sarah@campuswell.ac.id', 'password')" 
                    class="p-2 rounded-lg bg-surface-soft border border-line text-[11px] text-ink font-semibold hover:bg-lavender transition-colors">
                    dr. Sarah (Dokter)
                </button>
                <button type="button" @click="fill('admin@campuswell.ac.id', 'password')" 
                    class="p-2 rounded-lg bg-surface-soft border border-line text-[11px] text-ink font-semibold hover:bg-sky transition-colors">
                    Admin IT
                </button>
            </div>
        </div>

        <!-- Back to Student Login -->
        <div class="text-center pt-2 text-xs text-ink-mute">
            <span>Bukan staf klinik?</span>
            <a href="{{ route('login') }}" class="font-semibold text-ink hover:underline ml-1">Masuk sebagai Mahasiswa</a>
        </div>
    </div>
</x-layouts.guest>
