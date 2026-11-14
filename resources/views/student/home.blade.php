<x-layouts.student>
    <x-slot:title>Beranda - CampusWell Mahasiswa</x-slot:title>

    <div x-data="studentHomeHandler()" class="space-y-4">
        <!-- Header Greeting & Quick Log FAB Trigger -->
        <div class="flex items-center justify-between pt-1">
            <div>
                <h1 class="text-2xl font-bold text-ink tracking-tight">Kesehatanmu hari ini</h1>
                <p class="text-xs text-ink-mute mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
            
            <button 
                type="button" 
                @click="openFormModal()"
                class="w-12 h-12 rounded-full bg-ink text-white flex items-center justify-center shadow-float hover:scale-105 active:scale-95 transition-all shrink-0 cursor-pointer" 
                title="{{ $todayLog ? 'Ubah Log Hari Ini' : 'Isi Log Hari Ini' }}"
            >
                <i data-lucide="{{ $todayLog ? 'edit-3' : 'plus' }}" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- 1. Kartu Kesejahteraan (Sage) -->
        <x-card tone="sage" class="space-y-4 relative overflow-hidden">
            <div class="flex items-center justify-between">
                @if($todayEvaluation)
                    @php
                        $badgeLevel = match($todayEvaluation->final_level) {
                            'PERLU_PERHATIAN' => 'risk',
                            'PERHATIAN_MANDIRI' => 'self_care',
                            default => 'normal',
                        };
                        $badgeLabel = match($todayEvaluation->final_level) {
                            'PERLU_PERHATIAN' => 'Perlu Perhatian Klinis',
                            'PERHATIAN_MANDIRI' => 'Perhatian Mandiri',
                            default => 'Status Terjaga Baik',
                        };
                    @endphp
                    <x-badge-status :level="$badgeLevel" :label="$badgeLabel" />
                @else
                    <x-badge-status level="normal" label="Belum Ada Log Hari Ini" />
                @endif

                <span class="text-[11px] font-semibold text-ink-mute">
                    {{ $todayLog ? 'Tercatat ' . $todayLog->updated_at->format('H:i') : 'Rekomendasi Harian' }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-3 items-end">
                <div>
                    <span class="text-[11px] font-medium text-ink-soft block uppercase tracking-wider">Durasi Tidur</span>
                    <div class="flex items-baseline gap-1 mt-0.5">
                        <span class="text-4xl font-extrabold tracking-tight text-ink">
                            {{ $todayLog ? number_format($todayLog->sleep_hours, 1) : '-' }}
                        </span>
                        <span class="text-xs text-ink-soft font-semibold">jam</span>
                    </div>
                </div>

                <div class="text-right">
                    <span class="text-[11px] font-medium text-ink-soft block uppercase tracking-wider">Streak Kurang Tidur</span>
                    <div class="flex items-baseline justify-end gap-1 mt-0.5">
                        <span class="text-2xl font-bold {{ ($derived['sleep_streak'] ?? 0) >= 2 ? 'text-status-risk-fg' : 'text-ink' }}">
                            {{ $derived['sleep_streak'] ?? 0 }}
                        </span>
                        <span class="text-xs text-ink-soft font-medium">hari (&lt;6j)</span>
                    </div>
                </div>
            </div>

            <p class="text-xs text-ink-soft leading-relaxed pt-1 border-t border-line/40">
                @if($todayEvaluation && $todayEvaluation->final_level === 'PERLU_PERHATIAN')
                    Terdapat indikasi kelelahan atau gangguan lambung berulang. Konsultasi dokter klinik dianjurkan.
                @elseif($todayEvaluation && $todayEvaluation->final_level === 'PERHATIAN_MANDIRI')
                    Kondisi fisikmu memerlukan pemulihan mandiri: atur jadwal tidur lebih awal dan hindari pemicu asam lambung.
                @elseif($todayLog)
                    Pola istirahat dan nutrisimu terjaga seimbang. Terus pertahankan pola hidup sehat ini!
                @else
                    Luangkan waktu 45 detik untuk mengisi log kebiasaan agar sistem skrining dapat mendeteksi risiko sedini mungkin.
                @endif
            </p>
        </x-card>

        <!-- 2. Grid 4 Mini Cards -->
        <div class="grid grid-cols-2 gap-2.5">
            <!-- Tidur Mini Card -->
            <div class="bg-surface p-3.5 rounded-card border border-line/60 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-ink-mute mb-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider">Tidur</span>
                    <div class="w-6 h-6 rounded-full bg-sage/60 text-ink flex items-center justify-center">
                        <i data-lucide="moon" class="w-3.5 h-3.5"></i>
                    </div>
                </div>
                <div>
                    <div class="text-xl font-bold text-ink">
                        {{ $todayLog ? number_format($todayLog->sleep_hours, 1) . ' jam' : 'Belum diisi' }}
                    </div>
                    <span class="text-[11px] text-ink-mute">
                        {{ ($derived['sleep_streak'] ?? 0) > 1 ? ($derived['sleep_streak'] . ' hr beruntun <6j') : 'Target: 7-9 jam' }}
                    </span>
                </div>
            </div>

            <!-- Pola Makan Mini Card -->
            <div class="bg-surface p-3.5 rounded-card border border-line/60 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-ink-mute mb-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider">Makan</span>
                    <div class="w-6 h-6 rounded-full bg-lavender/60 text-ink flex items-center justify-center">
                        <i data-lucide="utensils" class="w-3.5 h-3.5"></i>
                    </div>
                </div>
                <div>
                    <div class="text-xl font-bold text-ink">
                        {{ $todayLog ? $todayLog->meal_freq . 'x sehari' : 'Belum diisi' }}
                    </div>
                    <span class="text-[11px] text-ink-mute truncate block">
                        {{ $todayLog && !empty($todayLog->food_categories) ? implode(', ', array_slice($todayLog->food_categories, 0, 2)) : 'Teratur 3x' }}
                    </span>
                </div>
            </div>

            <!-- Stres Mini Card -->
            <div class="bg-surface p-3.5 rounded-card border border-line/60 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-ink-mute mb-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider">Stres</span>
                    <div class="w-6 h-6 rounded-full bg-butter/60 text-ink flex items-center justify-center">
                        <i data-lucide="activity" class="w-3.5 h-3.5"></i>
                    </div>
                </div>
                <div>
                    <div class="text-xl font-bold text-ink">
                        {{ $todayLog ? 'Skala ' . $todayLog->stress_level . '/5' : 'Belum diisi' }}
                    </div>
                    <span class="text-[11px] text-ink-mute">
                        @php
                            $stressLabels = [1 => 'Tenang', 2 => 'Ringan', 3 => 'Menekan', 4 => 'Berat', 5 => 'Kewalahan'];
                        @endphp
                        {{ $todayLog ? ($stressLabels[$todayLog->stress_level] ?? 'Terkendali') : 'Terkendali' }}
                    </span>
                </div>
            </div>

            <!-- Kesegaran Mini Card -->
            <div class="bg-surface p-3.5 rounded-card border border-line/60 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between text-ink-mute mb-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider">Kesegaran</span>
                    <div class="w-6 h-6 rounded-full bg-sky/60 text-ink flex items-center justify-center">
                        <i data-lucide="sun" class="w-3.5 h-3.5"></i>
                    </div>
                </div>
                <div>
                    <div class="text-xl font-bold text-ink">
                        {{ $todayLog ? 'Skala ' . $todayLog->freshness . '/5' : 'Belum diisi' }}
                    </div>
                    <span class="text-[11px] text-ink-mute">
                        {{ $todayLog ? ($todayLog->freshness >= 4 ? 'Segar bugar' : ($todayLog->freshness <= 2 ? 'Lelah/Kurang segar' : 'Cukup segar')) : 'Bangun pagi' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 3. Kartu Aksi "Log Hari Ini" (Butter) -->
        <x-card tone="butter" class="space-y-2.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full {{ $todayLog ? 'bg-status-normal-bg text-status-normal-fg' : 'bg-ink text-white' }} flex items-center justify-center">
                        <i data-lucide="{{ $todayLog ? 'check' : 'clipboard-list' }}" class="w-4 h-4"></i>
                    </div>
                    <h3 class="text-sm font-bold text-ink">
                        {{ $todayLog ? 'Log Hari Ini Selesai' : 'Log Kebiasaan Hari Ini' }}
                    </h3>
                </div>
                <span class="text-[11px] font-bold {{ $todayLog ? 'text-status-normal-fg bg-status-normal-bg' : 'text-ink bg-surface' }} px-2.5 py-0.5 rounded-full border border-line/40">
                    {{ $todayLog ? 'Sudah Tercatat' : 'Belum Diisi' }}
                </span>
            </div>

            <p class="text-xs text-ink-soft leading-relaxed">
                @if($todayLog)
                    Kamu telah memperbarui log kebiasaan sehat hari ini pada pukul {{ $todayLog->updated_at->format('H:i') }} WIB. Kamu dapat mengubah nilai kapan saja jika ada perubahan.
                @else
                    Catat durasi tidur semalam, frekuensi makan, stres, dan keluhan fisik harianmu untuk menjaga kebugaran akademik.
                @endif
            </p>

            <div class="pt-1">
                @if($todayLog)
                    <button 
                        type="button" 
                        @click="openFormModal()"
                        class="w-full h-11 rounded-full bg-surface border border-line text-ink font-semibold text-xs flex items-center justify-center gap-2 hover:bg-surface-soft active:scale-[0.99] transition-all shadow-xs cursor-pointer"
                    >
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                        <span>Ubah Log Hari Ini</span>
                    </button>
                @else
                    <button 
                        type="button" 
                        @click="openFormModal()"
                        class="w-full h-11 rounded-full bg-ink text-white font-semibold text-xs flex items-center justify-center gap-2 hover:bg-ink/90 active:scale-[0.99] transition-all shadow-card cursor-pointer"
                    >
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Isi Log Sekarang (≤ 45 dtk)</span>
                    </button>
                @endif
            </div>
        </x-card>

        <!-- 4. Active Alert Banner (If Any Risk / Self Care Triggered & Not Dismissed) -->
        @if($todayEvaluation && !empty($todayEvaluation->alerts) && !$todayEvaluation->cooldown_suppressed)
            @foreach($todayEvaluation->alerts as $alert)
                @if(!($alert['suppressed'] ?? false))
                    <div x-data="{ dismissed: false }" x-show="!dismissed" x-transition class="space-y-2">
                        <x-alert-cds 
                            level="risk"
                            :title="$alert['title'] ?? 'Peringatan Risiko Kesehatan'"
                            :reasons="$alert['reasons'] ?? ['Terdeteksi pola kebiasaan yang membutuhkan perhatian.']"
                            :ruleIds="$alert['rule_ids'] ?? []"
                            actionLabel="Konsultasi ke Dokter"
                            actionRoute="{{ route('student.consultation') }}?alert={{ $alert['type'] ?? 'GENERAL' }}"
                        />
                        <div class="flex justify-end px-1">
                            <button 
                                type="button" 
                                @click="dismissAlert('{{ $alert['type'] }}', {{ $alert['score'] ?? 0 }}); dismissed = true"
                                class="text-[11px] font-medium text-ink-mute hover:text-ink hover:underline cursor-pointer flex items-center gap-1"
                            >
                                <i data-lucide="bell-off" class="w-3 h-3"></i>
                                <span>Tutup & Jangan Ingatkan (3 hari)</span>
                            </button>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif

        <!-- 5. Kartu Konsultasi Klinik Kampus (Lavender) -->
        <x-card tone="lavender" class="space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-ink text-white flex items-center justify-center">
                        <i data-lucide="stethoscope" class="w-4 h-4 text-lavender"></i>
                    </div>
                    <h3 class="text-sm font-bold text-ink">Layanan Klinik Kampus</h3>
                </div>

                @if($onlineDoctors->isNotEmpty())
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-status-normal-fg bg-status-normal-bg px-2.5 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-status-normal-fg animate-pulse"></span>
                        <span>{{ $onlineDoctors->count() }} Dokter Online</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-ink-mute bg-surface px-2.5 py-0.5 rounded-full border border-line/40">
                        <span>Jadwal Asinkron</span>
                    </span>
                @endif
            </div>

            <p class="text-xs text-ink-soft leading-relaxed">
                Butuh saran medis langsung dari tenaga kesehatan kampus? Mulai konsultasi tele-medis dengan data skrining 7 hari terlampir.
            </p>

            <div class="pt-1">
                <a 
                    href="{{ route('student.consultation') }}" 
                    class="w-full h-11 rounded-full bg-surface border border-line text-ink font-semibold text-xs flex items-center justify-center gap-2 hover:bg-surface-soft active:scale-[0.99] transition-all shadow-xs"
                >
                    <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                    <span>Buka Ruang Konsultasi</span>
                </a>
            </div>
        </x-card>

        <!-- ========================================================================= -->
        <!-- MODAL M-04: FORM HABIT TRACKER (Bottom Sheet Mobile / Center Modal Desktop) -->
        <!-- ========================================================================= -->
        <div 
            x-show="formModalOpen" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog" 
            aria-modal="true"
        >
            <!-- Backdrop -->
            <div 
                x-show="formModalOpen"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-ink/40 backdrop-blur-xs"
                @click="confirmCloseFormModal()"
            ></div>

            <div class="min-h-full flex items-end md:items-center justify-center md:p-4 text-center">
                <div 
                    x-show="formModalOpen"
                    x-transition:enter="ease-out duration-250"
                    x-transition:enter-start="translate-y-full md:translate-y-0 md:scale-95 opacity-0"
                    x-transition:enter-end="translate-y-0 md:scale-100 opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="translate-y-0 md:scale-100 opacity-100"
                    x-transition:leave-end="translate-y-full md:translate-y-0 md:scale-95 opacity-0"
                    class="w-full md:max-w-[480px] bg-canvas text-left rounded-t-[28px] md:rounded-[28px] shadow-float overflow-hidden flex flex-col max-h-[92vh] md:max-h-[88vh] border border-line/60 relative"
                >
                    <!-- Drag Handle (Mobile) -->
                    <div class="md:hidden flex justify-center pt-3 pb-1 bg-surface">
                        <div class="w-12 h-1.5 bg-line rounded-full"></div>
                    </div>

                    <!-- Modal Header -->
                    <div class="px-5 py-3.5 bg-surface border-b border-line/50 flex items-center justify-between shrink-0">
                        <div>
                            <h2 class="text-base font-bold text-ink">Log Kebiasaan Hari Ini</h2>
                            <p class="text-[11px] text-ink-mute">{{ now()->translatedFormat('l, d F Y') }}</p>
                        </div>
                        <button 
                            type="button" 
                            @click="confirmCloseFormModal()" 
                            class="w-8 h-8 rounded-full bg-surface-soft border border-line flex items-center justify-center text-ink-mute hover:text-ink cursor-pointer"
                        >
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <!-- Modal Scrollable Body -->
                    <form id="habitForm" @submit.prevent="submitHabitLog()" class="overflow-y-auto p-4 space-y-4 flex-1">
                        <!-- Error Banner (If Any) -->
                        <div x-show="errorMessage" x-cloak class="p-3 rounded-control bg-status-risk-bg text-status-risk-fg text-xs flex items-center gap-2">
                            <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                            <span x-text="errorMessage"></span>
                        </div>

                        <!-- 1. D1: Durasi Tidur (Sage Block) -->
                        <div class="p-4 rounded-card-sm bg-sage/40 border border-sage/60 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-ink uppercase tracking-wider flex items-center gap-1.5">
                                    <i data-lucide="moon" class="w-3.5 h-3.5 text-sage-strong"></i>
                                    <span>Durasi Tidur Semalam (D1)</span>
                                </label>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-2xl font-extrabold text-ink" x-text="formData.sleep_hours"></span>
                                    <span class="text-xs text-ink-mute font-semibold">jam</span>
                                </div>
                            </div>
                            
                            <input 
                                type="range" 
                                min="0" 
                                max="14" 
                                step="0.5" 
                                x-model.number="formData.sleep_hours"
                                class="w-full h-2 bg-white/80 rounded-lg appearance-none cursor-pointer accent-ink focus:outline-none"
                            >

                            <div class="flex justify-between text-[11px] text-ink-mute">
                                <span>Kurang (&lt;6j)</span>
                                <span>Ideal (7-9j)</span>
                                <span>Panjang (&gt;10j)</span>
                            </div>
                        </div>

                        <!-- 2. D2, D3, D4: Pola Makan (Lavender Block) -->
                        <div class="p-4 rounded-card-sm bg-lavender/40 border border-lavender/60 space-y-3.5">
                            <label class="text-xs font-bold text-ink uppercase tracking-wider flex items-center gap-1.5">
                                <i data-lucide="utensils" class="w-3.5 h-3.5 text-lavender-strong"></i>
                                <span>Pola Makan & Nutrisi</span>
                            </label>

                            <!-- D2: Frekuensi Makan -->
                            <div class="space-y-1.5">
                                <span class="text-xs font-semibold text-ink-soft block">Frekuensi Makan Utama (D2)</span>
                                <div class="grid grid-cols-6 gap-1 bg-surface p-1 rounded-control border border-line/60">
                                    <template x-for="n in [0, 1, 2, 3, 4, 5]" :key="n">
                                        <button 
                                            type="button" 
                                            @click="formData.meal_freq = n"
                                            :class="formData.meal_freq === n ? 'bg-ink text-white shadow-xs font-bold' : 'text-ink hover:bg-surface-soft font-medium'"
                                            class="h-9 rounded-lg text-xs transition-all flex items-center justify-center cursor-pointer"
                                            x-text="n === 5 ? '5+' : n"
                                        ></button>
                                    </template>
                                </div>
                            </div>

                            <!-- D3: Kategori Makanan Pemicu -->
                            <div class="space-y-1.5">
                                <span class="text-xs font-semibold text-ink-soft block">Makanan / Minuman Hari Ini (D3)</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="cat in ['Pedas', 'Asam', 'Manis', 'High-Caffeine', 'Netral']" :key="cat">
                                        <button 
                                            type="button" 
                                            @click="toggleFoodCategory(cat)"
                                            :class="formData.food_categories.includes(cat) ? 'bg-ink text-white font-semibold' : 'bg-surface text-ink border border-line/60 font-medium hover:bg-surface-soft'"
                                            class="px-3 py-1.5 rounded-full text-xs transition-all cursor-pointer select-none"
                                            x-text="cat === 'High-Caffeine' ? 'Kafein Tinggi (Kopi/Energi)' : cat"
                                        ></button>
                                    </template>
                                </div>
                            </div>

                            <!-- D4: Jarak Makan Terakhir ke Tidur -->
                            <div class="space-y-1.5 pt-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-ink-soft">Jarak Makan Malam ke Tidur (D4)</span>
                                    <span class="text-xs font-bold text-ink" x-text="formData.no_late_eating ? 'Tidak makan malam' : formData.late_eating_hours + ' jam'"></span>
                                </div>

                                <div x-show="!formData.no_late_eating" class="py-1">
                                    <input 
                                        type="range" 
                                        min="0" 
                                        max="6" 
                                        step="1" 
                                        x-model.number="formData.late_eating_hours"
                                        class="w-full h-2 bg-white/80 rounded-lg appearance-none cursor-pointer accent-ink focus:outline-none"
                                    >
                                    <div class="flex justify-between text-[10px] text-ink-mute mt-1">
                                        <span>0j (Langsung tidur)</span>
                                        <span>&ge; 2j (Aman)</span>
                                        <span>6j+</span>
                                    </div>
                                </div>

                                <label class="flex items-center gap-2 cursor-pointer pt-1">
                                    <input 
                                        type="checkbox" 
                                        x-model="formData.no_late_eating" 
                                        class="rounded text-ink focus:ring-ink"
                                    >
                                    <span class="text-xs text-ink-soft">Tidak makan malam hari ini</span>
                                </label>
                            </div>
                        </div>

                        <!-- 3. D5, D6, D7: Stres, Keluhan, Kesegaran (Butter Block) -->
                        <div class="p-4 rounded-card-sm bg-butter/40 border border-butter/60 space-y-3.5">
                            <label class="text-xs font-bold text-ink uppercase tracking-wider flex items-center gap-1.5">
                                <i data-lucide="activity" class="w-3.5 h-3.5 text-butter-strong"></i>
                                <span>Kondisi Fisik & Beban Mental</span>
                            </label>

                            <!-- D5: Tingkat Stres (1-5) -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-ink-soft">Tingkat Stres Akademik / Harian (D5)</span>
                                    <span class="text-xs font-bold text-ink" x-text="'Level ' + formData.stress_level"></span>
                                </div>
                                <div class="grid grid-cols-5 gap-1 bg-surface p-1 rounded-control border border-line/60">
                                    <template x-for="s in [1, 2, 3, 4, 5]" :key="s">
                                        <button 
                                            type="button" 
                                            @click="formData.stress_level = s"
                                            :class="formData.stress_level === s ? 'bg-ink text-white font-bold' : 'text-ink hover:bg-surface-soft font-medium'"
                                            class="h-9 rounded-lg text-xs transition-all flex items-center justify-center cursor-pointer"
                                            x-text="s"
                                        ></button>
                                    </template>
                                </div>
                                <p class="text-[11px] text-ink-soft italic" x-text="getStressDescription(formData.stress_level)"></p>
                            </div>

                            <!-- D6: Chip Keluhan Harian -->
                            <div class="space-y-1.5 pt-1">
                                <span class="text-xs font-semibold text-ink-soft block">Keluhan Fisik Hari Ini (D6)</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="item in [
                                        { key: 'perih', label: 'Perih ulu hati' },
                                        { key: 'asam_naik', label: 'Asam lambung naik' },
                                        { key: 'mual', label: 'Mual' },
                                        { key: 'kembung', label: 'Kembung' },
                                        { key: 'lelah', label: 'Lelah berat' },
                                        { key: 'konsentrasi', label: 'Sulit konsentrasi' },
                                        { key: 'tidak_ada', label: 'Tidak ada keluhan' }
                                    ]" :key="item.key">
                                        <button 
                                            type="button" 
                                            @click="toggleComplaint(item.key)"
                                            :class="formData.complaints.includes(item.key) ? 'bg-ink text-white font-semibold' : 'bg-surface text-ink border border-line/60 font-medium hover:bg-surface-soft'"
                                            class="px-2.5 py-1.5 rounded-full text-xs transition-all cursor-pointer select-none"
                                            x-text="item.label"
                                        ></button>
                                    </template>
                                </div>
                            </div>

                            <!-- D7: Kesegaran Saat Bangun -->
                            <div class="space-y-1.5 pt-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-ink-soft">Kesegaran Saat Bangun Tidur (D7)</span>
                                    <span class="text-xs font-bold text-ink" x-text="getFreshnessLabel(formData.freshness)"></span>
                                </div>
                                <div class="grid grid-cols-5 gap-1 bg-surface p-1 rounded-control border border-line/60">
                                    <template x-for="f in [1, 2, 3, 4, 5]" :key="f">
                                        <button 
                                            type="button" 
                                            @click="formData.freshness = f"
                                            :class="formData.freshness === f ? 'bg-ink text-white font-bold' : 'text-ink hover:bg-surface-soft font-medium'"
                                            class="h-9 rounded-lg text-xs transition-all flex items-center justify-center cursor-pointer"
                                            x-text="f"
                                        ></button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Sticky Submit Footer Inside Modal -->
                        <div class="pt-2">
                            <button 
                                type="submit" 
                                :disabled="isSubmitting"
                                class="w-full h-12 rounded-full bg-ink text-white font-semibold text-sm flex items-center justify-center gap-2 hover:bg-ink/90 active:scale-[0.99] transition-all shadow-float cursor-pointer disabled:opacity-50"
                            >
                                <template x-if="!isSubmitting">
                                    <span class="flex items-center gap-2">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                                        <span>Simpan & Lihat Hasil Skrining</span>
                                    </span>
                                </template>
                                <template x-if="isSubmitting">
                                    <span class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                        <span>Mengevaluasi Aturan Klinis...</span>
                                    </span>
                                </template>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL M-05: HASIL EVALUASI CDS (3 Varian: Normal, Perhatian Mandiri, Risiko) -->
        <!-- ========================================================================= -->
        <div 
            x-show="resultModalOpen" 
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog" 
            aria-modal="true"
        >
            <!-- Backdrop: Tidak bisa diklik jika level PERLU_PERHATIAN -->
            <div 
                x-show="resultModalOpen"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-ink/40 backdrop-blur-xs"
                @click="if (evaluationResult?.final_level !== 'PERLU_PERHATIAN') closeResultModal()"
            ></div>

            <div class="min-h-full flex items-end md:items-center justify-center md:p-4 text-center">
                <div 
                    x-show="resultModalOpen"
                    x-transition:enter="ease-out duration-250"
                    x-transition:enter-start="translate-y-full md:translate-y-0 md:scale-95 opacity-0"
                    x-transition:enter-end="translate-y-0 md:scale-100 opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="translate-y-0 md:scale-100 opacity-100"
                    x-transition:leave-end="translate-y-full md:translate-y-0 md:scale-95 opacity-0"
                    class="w-full md:max-w-[440px] bg-canvas text-left rounded-t-[28px] md:rounded-[28px] shadow-float overflow-hidden flex flex-col max-h-[92vh] border border-line/60 relative p-5 space-y-4"
                >
                    <!-- Drag Handle Mobile (Only if dismissible) -->
                    <template x-if="evaluationResult?.final_level !== 'PERLU_PERHATIAN'">
                        <div class="md:hidden flex justify-center -mt-2 pb-1">
                            <div class="w-12 h-1.5 bg-line rounded-full"></div>
                        </div>
                    </template>

                    <!-- VARIAN 1: NORMAL -->
                    <template x-if="evaluationResult?.final_level === 'NORMAL'">
                        <div class="space-y-4 text-center py-2">
                            <div class="w-16 h-16 rounded-full bg-status-normal-bg text-status-normal-fg flex items-center justify-center mx-auto shadow-xs">
                                <i data-lucide="check" class="w-8 h-8"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-ink">Kebiasaanmu Hari Ini Baik!</h3>
                                <p class="text-xs text-ink-soft leading-relaxed mt-1">
                                    Durasi istirahat, ritme makan, dan tingkat stresmu berada dalam rentang aman. Pertahankan kebiasaan ini demi konsentrasi belajar yang prima.
                                </p>
                            </div>

                            <div class="pt-2 space-y-2">
                                <button 
                                    type="button" 
                                    @click="closeResultModal()"
                                    class="w-full h-11 rounded-full bg-ink text-white font-semibold text-xs flex items-center justify-center shadow-card cursor-pointer hover:bg-ink/90"
                                >
                                    Selesai
                                </button>
                                <a 
                                    href="{{ route('student.history') }}" 
                                    class="w-full h-10 rounded-full bg-surface border border-line text-ink font-semibold text-xs flex items-center justify-center hover:bg-surface-soft"
                                >
                                    Lihat Tren Riwayat
                                </a>
                            </div>
                        </div>
                    </template>

                    <!-- VARIAN 2: PERHATIAN MANDIRI -->
                    <template x-if="evaluationResult?.final_level === 'PERHATIAN_MANDIRI'">
                        <div class="space-y-4 py-1">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-status-self-bg text-status-self-fg flex items-center justify-center shrink-0">
                                    <i data-lucide="info" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-status-self-fg">Saran Pemulihan</span>
                                    <h3 class="text-base font-bold text-ink">Perhatian Mandiri Diperlukan</h3>
                                </div>
                            </div>

                            <div class="p-3.5 rounded-card-sm bg-butter/40 border border-butter/60 space-y-2 text-xs text-ink-soft">
                                <span class="font-bold text-ink block">Catatan Klinis Terpantau:</span>
                                <ul class="list-disc list-inside space-y-1 text-[11px] leading-relaxed">
                                    <template x-for="(reason, idx) in evaluationResult?.reasons" :key="idx">
                                        <li x-text="reason"></li>
                                    </template>
                                </ul>
                            </div>

                            <div class="pt-2 space-y-2">
                                <button 
                                    type="button" 
                                    @click="closeResultModal()"
                                    class="w-full h-11 rounded-full bg-ink text-white font-semibold text-xs flex items-center justify-center shadow-card cursor-pointer hover:bg-ink/90"
                                >
                                    Pahami & Selesai
                                </button>
                                <a 
                                    href="{{ route('student.history') }}" 
                                    class="w-full h-10 rounded-full bg-surface border border-line text-ink font-semibold text-xs flex items-center justify-center hover:bg-surface-soft"
                                >
                                    Lihat Grafik Riwayat
                                </a>
                            </div>
                        </div>
                    </template>

                    <!-- VARIAN 3: PERLU PERHATIAN (RISK) -->
                    <template x-if="evaluationResult?.final_level === 'PERLU_PERHATIAN'">
                        <div class="space-y-4 py-1">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-status-risk-bg text-status-risk-fg flex items-center justify-center shrink-0">
                                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-status-risk-fg">Skrining Risiko Terdeteksi</span>
                                    <h3 class="text-base font-bold text-ink">Perlu Perhatian Tenaga Kesehatan</h3>
                                </div>
                            </div>

                            <!-- List of alerts -->
                            <div class="space-y-2">
                                <template x-for="(al, i) in evaluationResult?.alerts" :key="i">
                                    <div class="p-3 rounded-card-sm bg-status-risk-bg/40 border border-status-risk-fg/20 space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-status-risk-fg" x-text="al.title"></span>
                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-status-risk-bg text-status-risk-fg" x-text="'Skor: ' + al.score"></span>
                                        </div>
                                        <ul class="text-[11px] text-ink-soft list-disc list-inside space-y-0.5">
                                            <template x-for="(rsn, rIdx) in al.reasons" :key="rIdx">
                                                <li x-text="rsn"></li>
                                            </template>
                                        </ul>
                                    </div>
                                </template>
                            </div>

                            <p class="text-[11px] text-ink-mute leading-relaxed">
                                Kamu sangat dianjurkan untuk berkonsultasi dengan dokter klinik kampus. Data habit 7 hari dan alert ini akan otomatis dilampirkan agar dokter dapat memberikan arahan tepat.
                            </p>

                            <div class="pt-2 space-y-2">
                                <a 
                                    :href="'{{ route('student.consultation') }}?alert=' + (evaluationResult?.alerts?.[0]?.type || 'GENERAL')"
                                    class="w-full h-11 rounded-full bg-status-risk-fg text-white font-bold text-xs flex items-center justify-center gap-2 shadow-card cursor-pointer hover:bg-status-risk-fg/90"
                                >
                                    <i data-lucide="stethoscope" class="w-4 h-4"></i>
                                    <span>Rekomendasi Konsultasi Dokter</span>
                                </a>
                                <button 
                                    type="button" 
                                    @click="closeResultModal()"
                                    class="w-full h-10 rounded-full bg-surface border border-line text-ink font-semibold text-xs flex items-center justify-center hover:bg-surface-soft cursor-pointer"
                                >
                                    Nanti Saja (Lihat Beranda)
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- Clinical Disclaimer (Standar Spesifikasi) -->
                    <div class="pt-2 border-t border-line/50 text-center">
                        <p class="text-[10px] text-ink-mute leading-relaxed">
                            <span class="font-semibold text-ink-soft">Disclaimer Klinis:</span> Hasil ini merupakan skrining awal dan bukan diagnosis medis. Keputusan diagnosis dan terapi ditentukan oleh dokter.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Alpine.js Page Logic Handler -->
    <script>
        function studentHomeHandler() {
            return {
                formModalOpen: false,
                resultModalOpen: false,
                isSubmitting: false,
                errorMessage: '',
                formData: {
                    sleep_hours: {{ $todayLog ? $todayLog->sleep_hours : 7.0 }},
                    meal_freq: {{ $todayLog ? $todayLog->meal_freq : 3 }},
                    food_categories: {!! json_encode($todayLog ? $todayLog->food_categories : ['Netral']) !!},
                    late_eating_hours: {{ $todayLog && $todayLog->late_eating_hours !== null ? $todayLog->late_eating_hours : 3 }},
                    no_late_eating: {{ $todayLog && $todayLog->late_eating_hours === null ? 'true' : 'false' }},
                    stress_level: {{ $todayLog ? $todayLog->stress_level : 2 }},
                    complaints: {!! json_encode($todayLog ? $todayLog->complaints : ['tidak_ada']) !!},
                    freshness: {{ $todayLog ? $todayLog->freshness : 4 }},
                },
                evaluationResult: {!! json_encode($todayEvaluation ? [
                    'final_level' => $todayEvaluation->final_level,
                    'fatigue_score' => $todayEvaluation->fatigue_score,
                    'fatigue_level' => $todayEvaluation->fatigue_level,
                    'gastric_score' => $todayEvaluation->gastric_score,
                    'gastric_level' => $todayEvaluation->gastric_level,
                    'alerts' => $todayEvaluation->alerts ?? [],
                    'reasons' => $todayEvaluation->reasons ?? [],
                    'rules_triggered' => $todayEvaluation->rule_ids ?? [],
                    'cooldown_suppressed' => $todayEvaluation->cooldown_suppressed,
                ] : null) !!},

                openFormModal() {
                    this.errorMessage = '';
                    this.formModalOpen = true;
                    document.body.classList.add('overflow-hidden');
                    this.$nextTick(() => { window.initIcons && window.initIcons(); });
                },

                confirmCloseFormModal() {
                    this.formModalOpen = false;
                    document.body.classList.remove('overflow-hidden');
                },

                closeResultModal() {
                    this.resultModalOpen = false;
                    document.body.classList.remove('overflow-hidden');
                    // Muat ulang beranda agar status terbaru tampil
                    window.location.reload();
                },

                toggleFoodCategory(cat) {
                    if (cat === 'Netral') {
                        this.formData.food_categories = ['Netral'];
                        return;
                    }
                    this.formData.food_categories = this.formData.food_categories.filter(c => c !== 'Netral');
                    if (this.formData.food_categories.includes(cat)) {
                        this.formData.food_categories = this.formData.food_categories.filter(c => c !== cat);
                        if (this.formData.food_categories.length === 0) {
                            this.formData.food_categories = ['Netral'];
                        }
                    } else {
                        this.formData.food_categories.push(cat);
                    }
                },

                toggleComplaint(comp) {
                    if (comp === 'tidak_ada') {
                        this.formData.complaints = ['tidak_ada'];
                        return;
                    }
                    this.formData.complaints = this.formData.complaints.filter(c => c !== 'tidak_ada');
                    if (this.formData.complaints.includes(comp)) {
                        this.formData.complaints = this.formData.complaints.filter(c => c !== comp);
                        if (this.formData.complaints.length === 0) {
                            this.formData.complaints = ['tidak_ada'];
                        }
                    } else {
                        this.formData.complaints.push(comp);
                    }
                },

                getStressDescription(lvl) {
                    const desc = {
                        1: '1: Santai / Tenang tanpa tekanan berarti.',
                        2: '2: Ringan, tugas normal dan terkendali.',
                        3: '3: Cukup menekan tapi masih terkendali.',
                        4: '4: Berat, mulai mengganggu tidur, makan, atau fokus.',
                        5: '5: Kewalahan, sulit berkonsentrasi atau tidur nyenyak.'
                    };
                    return desc[lvl] || '';
                },

                getFreshnessLabel(lvl) {
                    const labels = {
                        1: '1: Sangat tidak segar / lemas',
                        2: '2: Kurang segar',
                        3: '3: Cukup segar',
                        4: '4: Segar bugar',
                        5: '5: Sangat berenergi'
                    };
                    return labels[lvl] || '';
                },

                async submitHabitLog() {
                    this.isSubmitting = true;
                    this.errorMessage = '';

                    const payload = {
                        sleep_hours: parseFloat(this.formData.sleep_hours),
                        meal_freq: parseInt(this.formData.meal_freq),
                        food_categories: this.formData.food_categories,
                        late_eating_hours: this.formData.no_late_eating ? null : parseInt(this.formData.late_eating_hours),
                        stress_level: parseInt(this.formData.stress_level),
                        complaints: this.formData.complaints,
                        freshness: parseInt(this.formData.freshness),
                    };

                    try {
                        const response = await fetch('{{ route('student.habit-logs.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message || 'Gagal menyimpan data habit harian.');
                        }

                        // Berhasil simpan
                        this.evaluationResult = data.evaluation;
                        this.formModalOpen = false;

                        // Buka modal M-05 (Hasil Evaluasi CDS)
                        this.resultModalOpen = true;
                        this.$nextTick(() => { window.initIcons && window.initIcons(); });

                    } catch (err) {
                        this.errorMessage = err.message || 'Terjadi kesalahan sistem, silakan coba lagi.';
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                async dismissAlert(alertType, score) {
                    try {
                        await fetch('{{ route('student.alerts.dismiss') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ alert_type: alertType, score: score })
                        });
                    } catch (e) {
                        console.error('Failed to dismiss alert:', e);
                    }
                }
            };
        }
    </script>
</x-layouts.student>
