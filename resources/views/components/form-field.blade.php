@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'required' => false,
    'placeholder' => '',
    'hint' => null,
])

<div class="space-y-1.5">
    @if($label)
        <label for="{{ $name }}" class="block text-xs font-semibold text-ink uppercase tracking-wider">
            {{ $label }}
            @if($required) <span class="text-status-risk-fg">*</span> @endif
        </label>
    @endif

    <div class="relative">
        <input 
            type="{{ $type }}" 
            name="{{ $name }}" 
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            {{ $attributes->merge(['class' => 'w-full h-12 px-4 rounded-control bg-surface border border-line text-sm text-ink placeholder:text-ink-mute focus:outline-none focus:ring-2 focus:ring-ink focus:border-transparent transition-all disabled:bg-surface-soft disabled:text-ink-mute']) }}
        >
    </div>

    @if($hint)
        <p class="text-[11px] text-ink-mute">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="text-xs font-medium text-status-risk-fg flex items-center gap-1 mt-1">
            <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
            <span>{{ $message }}</span>
        </p>
    @enderror
</div>
