@props([
    'isMe' => false,
    'message' => '',
    'time' => '',
    'senderName' => null,
])

<div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }} my-1.5">
    @if($senderName && !$isMe)
        <span class="text-[11px] font-semibold text-ink-mute mb-1 px-1">{{ $senderName }}</span>
    @endif

    <div class="{{ $isMe 
        ? 'bg-ink text-white rounded-2xl rounded-tr-xs shadow-card' 
        : 'bg-surface border border-line text-ink rounded-2xl rounded-tl-xs shadow-card' }} px-4 py-2.5 max-w-[82%] text-sm leading-relaxed break-words">
        {{ $message }}
        {{ $slot }}
    </div>

    @if($time)
        <span class="text-[10px] text-ink-mute mt-1 px-1">
            {{ $time }}
        </span>
    @endif
</div>
