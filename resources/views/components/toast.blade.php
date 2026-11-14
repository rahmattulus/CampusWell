<div 
    x-data="{
        messages: [],
        add(message, type = 'info') {
            const id = Date.now();
            this.messages.push({ id, message, type });
            this.$nextTick(() => { window.initIcons && window.initIcons(); });
            setTimeout(() => {
                this.remove(id);
            }, 4000);
        },
        remove(id) {
            this.messages = this.messages.filter(msg => msg.id !== id);
        }
    }"
    x-init="
        @if(session('success'))
            add({{ json_encode(session('success')) }}, 'success');
        @endif
        @if(session('error'))
            add({{ json_encode(session('error')) }}, 'error');
        @endif
        @if(session('info'))
            add({{ json_encode(session('info')) }}, 'info');
        @endif
        @if(session('status'))
            add({{ json_encode(session('status')) }}, 'info');
        @endif
    "
    x-on:toast.window="add($event.detail.message, $event.detail.type || 'info')"
    class="fixed top-4 left-1/2 -translate-x-1/2 md:left-auto md:right-4 md:translate-x-0 z-50 flex flex-col gap-2 pointer-events-none w-[90%] max-w-sm"
>
    <template x-for="msg in messages" :key="msg.id">
        <div 
            x-show="true"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            :class="{
                'bg-surface border-status-normal-fg/40 text-ink': msg.type === 'success',
                'bg-surface border-status-risk-fg/40 text-ink': msg.type === 'error',
                'bg-surface border-status-self-fg/40 text-ink': msg.type === 'warning',
                'bg-surface border-line text-ink': msg.type === 'info'
            }"
            class="pointer-events-auto p-3.5 rounded-control shadow-float border flex items-center gap-3 bg-surface"
        >
            <template x-if="msg.type === 'success'">
                <div class="w-7 h-7 rounded-full bg-status-normal-bg text-status-normal-fg flex items-center justify-center shrink-0">
                    <i data-lucide="check" class="w-4 h-4"></i>
                </div>
            </template>
            <template x-if="msg.type === 'error'">
                <div class="w-7 h-7 rounded-full bg-status-risk-bg text-status-risk-fg flex items-center justify-center shrink-0">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                </div>
            </template>
            <template x-if="msg.type === 'warning'">
                <div class="w-7 h-7 rounded-full bg-status-self-bg text-status-self-fg flex items-center justify-center shrink-0">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                </div>
            </template>
            <template x-if="msg.type === 'info'">
                <div class="w-7 h-7 rounded-full bg-sky text-ink flex items-center justify-center shrink-0">
                    <i data-lucide="info" class="w-4 h-4"></i>
                </div>
            </template>

            <p class="text-xs font-medium text-ink flex-1" x-text="msg.message"></p>

            <button type="button" @click="remove(msg.id)" class="text-ink-mute hover:text-ink">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
        </div>
    </template>
</div>
