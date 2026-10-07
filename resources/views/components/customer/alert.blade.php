@props(['type' => 'info', 'title' => null, 'message' => null, 'errors' => []])

@php
    $styles = [
        'success' => ['wrap' => 'flash--success', 'icon' => 'check'],
        'error' => ['wrap' => 'flash--error', 'icon' => 'info'],
        'warning' => ['wrap' => 'flash--validation', 'icon' => 'info'],
        'info' => ['wrap' => 'flash--validation', 'icon' => 'info'],
    ];

    $style = $styles[$type] ?? $styles['info'];
@endphp

<div class="flash {{ $style['wrap'] }}" role="{{ $type === 'error' ? 'alert' : 'status' }}">
    <span class="flash__icon">
        <x-customer.icon :name="$style['icon']" :size="17"></x-customer.icon>
    </span>

    <div>
        @if ($title)
            <strong>{{ $title }}</strong>
        @endif

        @if ($message)
            <div>{{ $message }}</div>
        @endif

        @if (filled($errors))
            <ul class="mt-1 list-disc list-inside">
                @foreach ($errors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </div>
</div>