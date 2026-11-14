@props([
    'variant' => 'primary', // primary, secondary, danger, icon, fab
    'type' => 'button',
    'href' => null,
])

@php
$baseClasses = 'inline-flex items-center justify-center text-sm font-semibold transition-all active:scale-[0.98] cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed';

$variantClasses = match($variant) {
    'primary' => 'bg-ink text-white rounded-full h-12 px-6 hover:bg-black shadow-card',
    'secondary' => 'bg-surface border border-line text-ink rounded-full h-12 px-6 hover:bg-surface-soft shadow-card',
    'danger' => 'bg-status-risk-bg text-status-risk-fg rounded-full h-12 px-6 hover:brightness-95 border border-status-risk-fg/20',
    'icon' => 'w-11 h-11 rounded-full bg-surface border border-line text-ink hover:bg-surface-soft shadow-card p-2.5',
    'fab' => 'w-14 h-14 rounded-full bg-ink text-white shadow-float hover:scale-105 p-3.5',
    default => 'bg-ink text-white rounded-full h-12 px-6 hover:bg-black shadow-card',
};
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "$baseClasses $variantClasses gap-2"]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "$baseClasses $variantClasses gap-2"]) }}>
        {{ $slot }}
    </button>
@endif
