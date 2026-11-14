<x-layouts.guest>
    <x-slot:title>Pendaftaran Mahasiswa - CampusWell</x-slot:title>

    <div x-data="{
        step: {{ $errors->hasAny(['p1_gastric_history', 'p2_smoking', 'p3_nsaid_usage', 'p4_chronic_conditions', 'consent']) ? 2 : 1 }},
        name: '{{ old('name', '') }}',
        nim: '{{ old('nim', '') }}',
        email: '{{ old('email', '') }}',
        password: '',
        password_confirmation: '',
        p1: '{{ old('p1_gastric_history', 'Tidak') }}',
        p2: '{{ old('p2_smoking', 'Tidak') }}',
        p3: '{{ old('p3_nsaid_usage', 'Tidak') }}',
        goToStep2() {
            if (!this.name || !this.nim || !this.email || !this.password || !this.password_confirmation) {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: 'Harap lengkapi semua data akun pada langkah 1.', type: 'warning' }
                }));
                return;
            }
            if (this.password !== this.password_confirmation) {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: 'Konfirmasi kata sandi tidak cocok.', type: 'error' }
                }));
                return;
            }
            this.step = 2;
            this.$nextTick(() => { window.initIcons && window.initIcons(); });
        }
    }" class="space-y-6">

        <!-- Header & Step Indicator -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-semibold text-ink-mute uppercase tracking-wider">
                    Pendaftaran Mahasiswa
                </span>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-surface-soft border border-line text-ink" x-text="'Langkah ' + step + ' dari 2'"></span>
            </div>

            <div class="grid grid-cols-2 gap-2 mb-4">
                <div class="h-1.5 rounded-full transition-all" :class="step >= 1 ? 'bg-ink' : 'bg-line'"></div>
                <div class="h-1.5 rounded-full transition-all" :class="step >= 2 ? 'bg-ink' : 'bg-line'"></div>
            </div>

            <h2 class="text-xl font-bold text-ink" x-text="step === 1 ? 'Data Diri & Akun' : 'Profil Faktor Risiko Kesehatan'"></h2>
            <p class="text-xs text-ink-mute mt-1" x-text="step === 1 ? 'Lengkapi identitas kemahasiswaanmu untuk membuat akun.' : 'Data ini wajib untuk akurasi skrining awal kebiasaan sehatmu.'"></p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- ================= LANGKAH 1: DATA AKUN ================= -->
            <div x-show="step === 1" class="space-y-4">
                <!-- Nama Lengkap -->
                <x-form-field 
                    name="name" 
                    label="Nama Lengkap" 
                    placeholder="Contoh: Ahmad Fauzi" 
                    x-model="name"
                    required 
                />

                <!-- NIM -->
                <x-form-field 
                    name="nim" 
                    label="Nomor Induk Mahasiswa (NIM)" 
                    placeholder="Contoh: 2201011001" 
                    x-model="nim"
                    required 
                />

                <!-- Email Kampus -->
                <x-form-field 
                    name="email" 
                    label="Alamat Email" 
                    type="email" 
                    placeholder="nama@student.ac.id" 
                    x-model="email"
                    required 
                />

                <!-- Kata Sandi -->
                <div class="space-y-1.5">
                    <label for="password" class="block text-xs font-semibold text-ink uppercase tracking-wider">
                        Kata Sandi <span class="text-status-risk-fg">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        x-model="password"
                        placeholder="Minimal 8 karakter" 
                        class="w-full h-12 px-4 rounded-control bg-surface border border-line text-sm text-ink placeholder:text-ink-mute focus:outline-none focus:ring-2 focus:ring-ink focus:border-transparent transition-all"
                    >
                    @error('password')
                        <p class="text-xs font-medium text-status-risk-fg mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Konfirmasi Kata Sandi -->
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="block text-xs font-semibold text-ink uppercase tracking-wider">
                        Konfirmasi Kata Sandi <span class="text-status-risk-fg">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        x-model="password_confirmation"
                        placeholder="Ketik ulang kata sandi" 
                        class="w-full h-12 px-4 rounded-control bg-surface border border-line text-sm text-ink placeholder:text-ink-mute focus:outline-none focus:ring-2 focus:ring-ink focus:border-transparent transition-all"
                    >
                </div>

                <!-- Next Button -->
                <div class="pt-2">
                    <x-button type="button" variant="primary" class="w-full" @click="goToStep2()">
                        <span>Lanjut ke Profil Risiko</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </x-button>
                </div>
            </div>

            <!-- ================= LANGKAH 2: PROFIL FAKTOR RISIKO (v2.1) ================= -->
            <div x-show="step === 2" x-cloak class="space-y-5">
                <!-- P1: Riwayat Maag / GERD -->
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-ink uppercase tracking-wider">
                        P1. Riwayat Maag / GERD dari Dokter <span class="text-status-risk-fg">*</span>
                    </label>
                    <p class="text-[11px] text-ink-mute">Pernah didiagnosis gangguan asam lambung atau GERD oleh dokter?</p>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['Ya', 'Tidak', 'Tidak tahu'] as $opt)
                            <button type="button" @click="p1 = '{{ $opt }}'"
                                :class="p1 === '{{ $opt }}' ? 'bg-ink text-white font-semibold shadow-card' : 'bg-surface text-ink border-line hover:bg-surface-soft font-medium'"
                                class="h-11 rounded-control border text-xs transition-all flex items-center justify-center">
                                {{ $opt }}
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="p1_gastric_history" :value="p1">
                    @error('p1_gastric_history')
                        <p class="text-xs text-status-risk-fg">{{ $message }}</p>
                    @enderror
                </div>

                <!-- P2: Merokok -->
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-ink uppercase tracking-wider">
                        P2. Kebiasaan Merokok / Vape <span class="text-status-risk-fg">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['Tidak', 'Kadang', 'Rutin'] as $opt)
                            <button type="button" @click="p2 = '{{ $opt }}'"
                                :class="p2 === '{{ $opt }}' ? 'bg-ink text-white font-semibold shadow-card' : 'bg-surface text-ink border-line hover:bg-surface-soft font-medium'"
                                class="h-11 rounded-control border text-xs transition-all flex items-center justify-center">
                                {{ $opt }}
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="p2_smoking" :value="p2">
                    @error('p2_smoking')
                        <p class="text-xs text-status-risk-fg">{{ $message }}</p>
                    @enderror
                </div>

                <!-- P3: NSAID / Pereda Nyeri -->
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-ink uppercase tracking-wider">
                        P3. Konsumsi Rutin Obat Pereda Nyeri (NSAID) <span class="text-status-risk-fg">*</span>
                    </label>
                    <p class="text-[11px] text-ink-mute">Misalnya asam mefenamat, ibuprofen, natrium diklofenak.</p>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['Tidak', 'Kadang', 'Rutin'] as $opt)
                            <button type="button" @click="p3 = '{{ $opt }}'"
                                :class="p3 === '{{ $opt }}' ? 'bg-ink text-white font-semibold shadow-card' : 'bg-surface text-ink border-line hover:bg-surface-soft font-medium'"
                                class="h-11 rounded-control border text-xs transition-all flex items-center justify-center">
                                {{ $opt }}
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="p3_nsaid_usage" :value="p3">
                    @error('p3_nsaid_usage')
                        <p class="text-xs text-status-risk-fg">{{ $message }}</p>
                    @enderror
                </div>

                <!-- P4: Kondisi Kronis Lain (Opsional) -->
                <div class="space-y-1.5">
                    <label for="p4_chronic_conditions" class="block text-xs font-semibold text-ink uppercase tracking-wider">
                        P4. Kondisi Kesehatan Lainnya <span class="text-ink-mute font-normal lowercase">(opsional)</span>
                    </label>
                    <textarea 
                        name="p4_chronic_conditions" 
                        id="p4_chronic_conditions" 
                        rows="2"
                        placeholder="Alergi obat, asma, insomnia, dll. (hanya untuk konteks dokter)"
                        class="w-full px-4 py-2.5 rounded-control bg-surface border border-line text-sm text-ink placeholder:text-ink-mute focus:outline-none focus:ring-2 focus:ring-ink focus:border-transparent transition-all"
                    >{{ old('p4_chronic_conditions') }}</textarea>
                </div>

                <!-- Consent Agreement (FR-PRF-02) -->
                <div class="p-3.5 rounded-control bg-surface-soft border border-line space-y-2">
                    <label class="flex items-start gap-2.5 cursor-pointer select-none">
                        <input 
                            type="checkbox" 
                            name="consent" 
                            id="consent" 
                            value="1"
                            {{ old('consent') ? 'checked' : '' }}
                            required
                            class="w-4 h-4 mt-0.5 rounded border-line text-ink focus:ring-ink cursor-pointer accent-ink shrink-0"
                        >
                        <span class="text-xs text-ink leading-relaxed font-medium">
                            Saya menyetujui penyimpanan dan pemrosesan data profil dan kebiasaan kesehatan ini secara aman untuk evaluasi skrining awal di Klinik Kampus.
                        </span>
                    </label>
                    @error('consent')
                        <p class="text-xs text-status-risk-fg">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Navigation Buttons -->
                <div class="flex items-center gap-2 pt-2">
                    <x-button type="button" variant="secondary" class="flex-1" @click="step = 1">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        <span>Kembali</span>
                    </x-button>

                    <x-button type="submit" variant="primary" class="flex-1">
                        <span>Daftar Akun</span>
                        <i data-lucide="check" class="w-4 h-4"></i>
                    </x-button>
                </div>
            </div>
        </form>

        <!-- Back to Login -->
        <div class="text-center pt-2 text-xs text-ink-mute">
            <span>Sudah memiliki akun?</span>
            <a href="{{ route('login') }}" class="font-semibold text-ink hover:underline ml-1">Masuk di sini</a>
        </div>
    </div>
</x-layouts.guest>
