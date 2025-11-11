@props(['item', 'showIcon' => true, 'showTooltip' => true])

@php
    $duration = $item->duration;
    $durationClass = $item->duration_class;
    $exactDate = $item->date_occurred->format('F d, Y \a\t g:i A');
@endphp

@if ($duration)
    <span class="inline-flex items-center gap-1.5 {{ $durationClass }}"
        @if ($showTooltip) title="Reported on {{ $exactDate }}" @endif>
        @if ($showIcon)
            <x-icon name="clock" size="xs" class="flex-shrink-0" />
        @endif
        <span class="text-sm">{{ $duration }}</span>
    </span>
@endif
