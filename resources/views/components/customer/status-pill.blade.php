@props(['label', 'tone' => 'neutral'])

@php
    $toneClass = match ($tone) {
        'green' => 'status-pill--green',
        'gold' => 'status-pill--gold',
        'red' => 'status-pill--red',
        'blue' => 'status-pill--blue',
        default => 'status-pill--neutral',
    };
@endphp

<span {{ $attributes->merge(['class' => 'status-pill ' . $toneClass]) }}>{{ $label }}</span>
