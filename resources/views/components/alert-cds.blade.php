@props([
    'level' => 'normal', // normal, self, risk or uppercase enum
    'title' => null,
    'description' => null,
    'score' => null,
    'ruleIds' => [],
    'showDisclaimer' => true,
    'consultAction' => false,
])

@php
$normalizedLevel = match(strtoupper($level)) {
    'NORMAL' => 'normal',
    'PERHATIAN_MANDIRI', 'SELF' => 'self',
    'PERLU_PERHATIAN', 'RISK' => 'risk',
    default => 'normal',
};

$borderStrip = match($normalizedLevel) {
    'normal' => 'border-l-4 border-l-status-normal-fg bg-surface',
    'self' => 'border-l-4 border-l-status-self-fg bg-surface',
    'risk' => 'border-l-4 border-l-status-risk-fg bg-surface',
};

$icon = match($normalizedLevel) {
    'normal' => 'check-circle',
    'self' => 'info',
    'risk' => 'alert-triangle',
};

$badgeBg = match($normalizedLevel) {
    'normal' => 'bg-status-normal-bg text-status-normal-fg',
    'self' => 'bg-status-self-bg text-status-self-fg',
    'risk' => 'bg-status-risk-bg text-status-risk-fg',
};

$defaultTitle = match($normalizedLevel) {
    'normal' => 'Kebiasaan Sehat Terjaga',
    'self' => 'Perhatian Mandiri Diperlukan',
    'risk' => 'Perlu Perhatian Klinis',
};

$displayTitle = $title ?? $defaultTitle;
@endphp

<div {{ $attributes->merge(['class' => "rounded-card p-5 border border-line $borderStrip shadow-card relative space-y-3"]) }}>
    <div class="flex items-start gap-3.5">
        <!-- Icon Badge -->
        <div class="w-10 h-10 rounded-full {{ $badgeBg }} flex items-center justify-center shrink-0">
            <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-2 flex-wrap">
                <h4 class="text-sm font-semibold text-ink leading-snug">{{ $displayTitle }}</h4>
                @if($score !== null)
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $badgeBg }}">
                        Skor: {{ $score }}
                    </span>
                @endif
            </div>

            @if($description)
                <p class="text-xs text-ink-soft mt-1 leading-relaxed">{{ $description }}</p>
            @endif

            @if(!empty($ruleIds))
                <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                    <span class="text-[11px] text-ink-mute font-medium">Aturan terpicu:</span>
                    @foreach($ruleIds as $rid)
                        <span class="text-[10px] font-mono font-semibold px-1.5 py-0.5 rounded bg-surface-soft border border-line text-ink">
                            {{ $rid }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Actions / Buttons Slot -->
    @if($slot->isNotEmpty() || $consultAction)
        <div class="pt-2 border-t border-line/60 flex items-center justify-between gap-2 flex-wrap">
            @if($consultAction)
                <x-button variant="primary" href="{{ route('student.consultation') }}" class="w-full sm:w-auto h-10 text-xs px-5">
                    <i data-lucide="stethoscope" class="w-3.5 h-3.5"></i>
                    <span>Rekomendasi Konsultasi Dokter</span>
                </x-button>
            @endif
            {{ $slot }}
        </div>
    @endif

    @if($showDisclaimer)
        <p class="text-[10px] text-ink-mute italic pt-1">
            * Hasil ini merupakan skrining awal dan bukan diagnosis. Keputusan diagnosis dan terapi ditentukan oleh dokter.
        </p>
    @endif
</div>
