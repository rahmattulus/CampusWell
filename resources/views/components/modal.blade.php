@props([
    'name', // Unique modal identifier
    'title' => null,
    'subtitle' => null,
    'preventClose' => false, // If true, cannot close by tapping backdrop or Esc (e.g. CDS Perlu Perhatian)
    'maxWidth' => 'md', // sm, md, lg
])

@php
$maxWidthClass = match($maxWidth) {
    'sm' => 'md:max-w-[400px]',
    'lg' => 'md:max-w-[560px]',
    default => 'md:max-w-[480px]',
};
@endphp

<div 
    x-data="{ 
        show: false,
        open() {
            this.show = true;
            document.body.classList.add('overflow-hidden');
            this.$nextTick(() => { window.initIcons && window.initIcons(); });
        },
        close() {
            @if(!$preventClose)
                this.show = false;
                document.body.classList.remove('overflow-hidden');
            @endif
        }
    }"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') open()"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') close()"
    x-on:keydown.escape.window="@if(!$preventClose) close() @endif"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title-{{ $name }}"
    role="dialog"
    aria-modal="true"
>
    <!-- Dark Backdrop (40% opacity) -->
    <div 
        x-show="show"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="@if(!$preventClose) close() @endif"
        class="fixed inset-0 bg-ink/40 backdrop-blur-xs transition-opacity"
    ></div>

    <!-- Modal / Bottom Sheet Container -->
    <div class="fixed inset-0 z-10 overflow-y-auto flex items-end md:items-center justify-center p-0 md:p-4">
        <div 
            x-show="show"
            x-transition:enter="transition ease-out duration-200 transform"
            x-transition:enter-start="translate-y-full md:translate-y-4 md:scale-95 md:opacity-0"
            x-transition:enter-end="translate-y-0 md:scale-100 md:opacity-100"
            x-transition:leave="transition ease-in duration-150 transform"
            x-transition:leave-start="translate-y-0 md:scale-100 md:opacity-100"
            x-transition:leave-end="translate-y-full md:translate-y-4 md:scale-95 md:opacity-0"
            class="w-full {{ $maxWidthClass }} bg-surface rounded-t-[28px] md:rounded-card shadow-float border border-line flex flex-col max-h-[92vh] md:max-h-[90vh] overflow-hidden"
            @click.stop
        >
            <!-- Mobile Handle Bar -->
            <div class="md:hidden flex justify-center pt-3 pb-1 shrink-0">
                <div class="w-10 h-1 bg-line rounded-full"></div>
            </div>

            <!-- Sticky Header -->
            @if($title || !$preventClose)
                <div class="px-5 py-4 border-b border-line flex items-center justify-between shrink-0 bg-surface">
                    <div>
                        @if($title)
                            <h3 class="text-base font-semibold text-ink" id="modal-title-{{ $name }}">{{ $title }}</h3>
                        @endif
                        @if($subtitle)
                            <p class="text-xs text-ink-mute mt-0.5">{{ $subtitle }}</p>
                        @endif
                    </div>
                    @if(!$preventClose)
                        <button type="button" @click="close()" class="w-8 h-8 rounded-full bg-surface-soft hover:bg-line text-ink flex items-center justify-center transition-colors">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    @endif
                </div>
            @endif

            <!-- Scrollable Body -->
            <div class="p-5 overflow-y-auto flex-1 space-y-4">
                {{ $slot }}
            </div>

            <!-- Sticky Footer (Optional) -->
            @if(isset($footer))
                <div class="px-5 py-4 border-t border-line bg-surface shrink-0">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
