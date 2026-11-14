@props([
    'type' => 'card', // card, text, circle
    'count' => 1,
])

<div class="animate-pulse space-y-3">
    @for($i = 0; $i < $count; $i++)
        @if($type === 'card')
            <div class="h-28 bg-surface-soft rounded-card border border-line p-5">
                <div class="h-4 bg-line rounded w-1/3 mb-3"></div>
                <div class="h-8 bg-line rounded w-1/2 mb-2"></div>
                <div class="h-3 bg-line rounded w-2/3"></div>
            </div>
        @elseif($type === 'circle')
            <div class="w-10 h-10 bg-line rounded-full"></div>
        @else
            <div class="h-4 bg-line rounded w-full"></div>
        @endif
    @endfor
</div>
