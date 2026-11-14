@props([
    'name',
    'label',
    'min' => 0,
    'max' => 12,
    'step' => 0.5,
    'value' => 7.0,
    'unit' => 'jam',
    'hint' => null,
])

<div x-data="{ currentVal: {{ old($name, $value) }} }" class="space-y-2">
    <div class="flex items-center justify-between">
        <label for="{{ $name }}" class="text-xs font-semibold text-ink uppercase tracking-wider">
            {{ $label }}
        </label>
        <div class="flex items-baseline gap-1">
            <span class="text-2xl font-semibold text-ink" x-text="currentVal"></span>
            <span class="text-xs text-ink-mute font-medium">{{ $unit }}</span>
        </div>
    </div>

    <div class="relative py-2 flex items-center">
        <input 
            type="range" 
            name="{{ $name }}" 
            id="{{ $name }}"
            min="{{ $min }}" 
            max="{{ $max }}" 
            step="{{ $step }}" 
            x-model="currentVal"
            class="w-full h-2 bg-line rounded-lg appearance-none cursor-pointer accent-ink focus:outline-none"
        >
    </div>

    @if($hint)
        <p class="text-[11px] text-ink-mute">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="text-xs font-medium text-status-risk-fg flex items-center gap-1">
            <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
            <span>{{ $message }}</span>
        </p>
    @enderror
</div>
