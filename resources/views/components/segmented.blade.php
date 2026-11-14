@props([
    'name',
    'label' => null,
    'options' => [1, 2, 3, 4, 5],
    'value' => null,
    'hint' => null,
    'descriptions' => [],
])

@php
$initVal = old($name, $value ?? (is_array($options) ? reset($options) : 1));
@endphp

<div x-data="{ selected: {{ json_encode($initVal) }} }" class="space-y-2">
    @if($label)
        <div class="flex items-center justify-between">
            <label class="text-xs font-semibold text-ink uppercase tracking-wider">
                {{ $label }}
            </label>
            <template x-if="selected !== null">
                <span class="text-xs font-semibold text-ink bg-surface px-2 py-0.5 rounded-full border border-line" x-text="selected"></span>
            </template>
        </div>
    @endif

    <!-- Segmented Button Row -->
    <div class="flex items-center gap-1.5 p-1 bg-surface-soft rounded-control border border-line">
        <input type="hidden" name="{{ $name }}" :value="selected">
        
        @foreach($options as $val => $text)
            @php
                $optVal = is_int($val) ? $text : $val;
                $optLabel = is_int($val) ? $text : $text;
            @endphp
            <button 
                type="button" 
                @click="selected = {{ json_encode($optVal) }}; $dispatch('input', selected)"
                :class="selected == {{ json_encode($optVal) }} ? 'bg-ink text-white shadow-card font-semibold' : 'bg-surface text-ink hover:bg-surface-soft font-medium border border-line/40'"
                class="flex-1 h-11 rounded-lg text-sm transition-all flex items-center justify-center cursor-pointer select-none">
                {{ $optLabel }}
            </button>
        @endforeach
    </div>

    @if(!empty($descriptions))
        <div class="text-[11px] text-ink-mute flex justify-between px-1">
            <span>{{ reset($descriptions) }}</span>
            <span>{{ end($descriptions) }}</span>
        </div>
    @elseif($hint)
        <p class="text-[11px] text-ink-mute">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="text-xs font-medium text-status-risk-fg flex items-center gap-1 mt-1">
            <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
            <span>{{ $message }}</span>
        </p>
    @enderror
</div>
