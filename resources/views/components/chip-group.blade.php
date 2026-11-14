@props([
    'name',
    'label' => null,
    'options' => [],
    'exclusive' => null, // e.g. 'Netral' or 'tidak_ada'
    'selected' => [],
    'hint' => null,
])

@php
$initSelected = old(rtrim($name, '[]'), $selected);
if (!is_array($initSelected)) {
    $initSelected = $initSelected ? [$initSelected] : ($exclusive ? [$exclusive] : []);
}
@endphp

<div x-data="{
    selectedList: {{ json_encode($initSelected) }},
    exclusiveVal: {{ json_encode($exclusive) }},
    toggle(val) {
        if (this.exclusiveVal && val === this.exclusiveVal) {
            // Selecting exclusive option clears all other options
            this.selectedList = [val];
            return;
        }

        // If choosing a regular option, remove exclusive option if present
        if (this.exclusiveVal && this.selectedList.includes(this.exclusiveVal)) {
            this.selectedList = this.selectedList.filter(item => item !== this.exclusiveVal);
        }

        if (this.selectedList.includes(val)) {
            this.selectedList = this.selectedList.filter(item => item !== val);
            // If empty and exclusive exists, fallback to exclusive
            if (this.selectedList.length === 0 && this.exclusiveVal) {
                this.selectedList = [this.exclusiveVal];
            }
        } else {
            this.selectedList.push(val);
        }
    }
}" class="space-y-2">
    @if($label)
        <label class="block text-xs font-semibold text-ink uppercase tracking-wider">
            {{ $label }}
        </label>
    @endif

    <div class="flex flex-wrap gap-2">
        @foreach($options as $key => $opt)
            @php
                $val = is_string($key) ? $key : $opt;
                $display = is_string($key) ? $opt : $opt;
            @endphp
            <button 
                type="button" 
                @click="toggle('{{ $val }}')"
                :class="selectedList.includes('{{ $val }}') 
                    ? 'bg-ink text-white border-ink shadow-card' 
                    : 'bg-surface text-ink border-line hover:bg-surface-soft'"
                class="h-10 px-4 rounded-full border text-xs font-semibold transition-all flex items-center gap-1.5 cursor-pointer select-none">
                <template x-if="selectedList.includes('{{ $val }}')">
                    <i data-lucide="check" class="w-3.5 h-3.5 text-white"></i>
                </template>
                <span>{{ $display }}</span>
            </button>
        @endforeach
    </div>

    <!-- Hidden input array for form submit -->
    <template x-for="item in selectedList" :key="item">
        <input type="hidden" name="{{ str_ends_with($name, '[]') ? $name : $name . '[]' }}" :value="item">
    </template>

    @if($hint)
        <p class="text-[11px] text-ink-mute">{{ $hint }}</p>
    @endif

    @error(rtrim($name, '[]'))
        <p class="text-xs font-medium text-status-risk-fg flex items-center gap-1 mt-1">
            <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
            <span>{{ $message }}</span>
        </p>
    @enderror
</div>
