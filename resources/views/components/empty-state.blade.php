@props([
    'icon' => 'inbox',
    'title' => 'Belum Ada Data',
    'description' => 'Data untuk bagian ini belum tersedia saat ini.',
    'tone' => 'surface-soft',
])

<div {{ $attributes->merge(['class' => 'p-8 text-center flex flex-col items-center justify-center']) }}>
    <div class="w-14 h-14 rounded-full bg-{{ $tone }} text-ink flex items-center justify-center mb-3 shadow-card">
        <i data-lucide="{{ $icon }}" class="w-6 h-6 text-ink-soft"></i>
    </div>
    <h4 class="text-sm font-semibold text-ink">{{ $title }}</h4>
    <p class="text-xs text-ink-mute mt-1 max-w-xs">{{ $description }}</p>
    
    @if(isset($action))
        <div class="mt-4">
            {{ $action }}
        </div>
    @endif
</div>
