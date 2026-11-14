@props([
    'tone' => 'surface',
    'bordered' => false,
    'rounded' => 'card', // 'card' (24px) or 'card-sm' (16px)
])

@php
$toneClasses = match($tone) {
    'sage' => 'bg-sage text-ink',
    'lavender' => 'bg-lavender text-ink',
    'sky' => 'bg-sky text-ink',
    'butter' => 'bg-butter text-ink',
    'surface-soft' => 'bg-surface-soft text-ink',
    default => 'bg-surface text-ink',
};

$borderClass = $bordered ? 'border border-line' : '';
$roundedClass = $rounded === 'card-sm' ? 'rounded-card-sm' : 'rounded-card';
@endphp

<div {{ $attributes->merge(['class' => "$toneClasses $roundedClass p-5 shadow-card $borderClass relative"]) }}>
    {{ $slot }}
</div>
