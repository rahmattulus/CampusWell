@props([
    'level' => 'normal', // normal, self, risk or uppercase enum
    'label' => null,
    'size' => 'md', // sm, md
])

@php
$normalizedLevel = match(strtoupper($level)) {
    'NORMAL' => 'normal',
    'PERHATIAN_MANDIRI', 'SELF' => 'self',
    'PERLU_PERHATIAN', 'RISK' => 'risk',
    default => 'normal',
};

$classes = match($normalizedLevel) {
    'normal' => 'bg-status-normal-bg text-status-normal-fg border-status-normal-fg/20',
    'self' => 'bg-status-self-bg text-status-self-fg border-status-self-fg/20',
    'risk' => 'bg-status-risk-bg text-status-risk-fg border-status-risk-fg/20',
};

$icon = match($normalizedLevel) {
    'normal' => 'check-circle',
    'self' => 'info',
    'risk' => 'alert-triangle',
};

$defaultLabel = match($normalizedLevel) {
    'normal' => 'Normal',
    'self' => 'Perhatian Mandiri',
    'risk' => 'Perlu Perhatian',
};

$text = $label ?? $defaultLabel;
$sizeClasses = $size === 'sm' ? 'px-2 py-0.5 text-[11px] gap-1' : 'px-3 py-1 text-xs gap-1.5';
$iconSize = $size === 'sm' ? 'w-3 h-3' : 'w-3.5 h-3.5';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-semibold rounded-full border $classes $sizeClasses shrink-0"]) }}>
    <i data-lucide="{{ $icon }}" class="{{ $iconSize }}"></i>
    <span>{{ $text }}</span>
</span>
