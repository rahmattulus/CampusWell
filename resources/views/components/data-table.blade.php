@props([
    'headers' => [],
])

<div class="w-full overflow-hidden bg-surface rounded-card-sm border border-line shadow-card">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            @if(!empty($headers))
                <thead>
                    <tr class="border-b border-line bg-surface-soft/60">
                        @foreach($headers as $key => $header)
                            @php
                                $align = is_array($header) ? ($header['align'] ?? 'left') : 'left';
                                $title = is_array($header) ? ($header['label'] ?? '') : $header;
                                $alignClass = match($align) {
                                    'right' => 'text-right',
                                    'center' => 'text-center',
                                    default => 'text-left',
                                };
                            @endphp
                            <th scope="col" class="px-5 py-3.5 text-xs font-semibold text-ink-mute uppercase tracking-wider {{ $alignClass }}">
                                {{ $title }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
            @endif
            <tbody class="divide-y divide-line text-sm text-ink">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
