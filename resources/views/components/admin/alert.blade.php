@props([
    'type' => 'info',
    'title' => null,
    'message' => null,
    'errors' => [],
])

@php
    $styles = [
        'success' => ['wrap' => 'bg-[#F0FDF4] border-[#16A34A] text-[#166534]', 'icon' => 'check'],
        'error' => ['wrap' => 'bg-red-50 border-red-300 text-red-700', 'icon' => 'info'],
        'warning' => ['wrap' => 'bg-[#FEF9C3] border-[#EAB308] text-[#166534]', 'icon' => 'info'],
        'info' => ['wrap' => 'bg-base border-[#F8FAFC] text-gray-700', 'icon' => 'info'],
    ];

    $style = $styles[$type] ?? $styles['info'];
    $icons = [
        'check' => '<path d="m5 12.5 4.2 4.2L19 7"></path>',
        'info' => '<circle cx="12" cy="12" r="8.5"></circle><path d="M12 11v5M12 8h.01"></path>',
    ];
@endphp

<div class="alert alert--{{ $type }} {{ $style['wrap'] }} px-4 py-3 rounded-md" role="{{ $type === 'error' ? 'alert' : 'status' }}">
    <div class="flex items-start gap-2.5">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
             class="shrink-0 mt-0.5" aria-hidden="true">
            {!! $icons[$style['icon']] !!}
        </svg>

        <div class="min-w-0 flex-1 text-sm leading-relaxed">
            @if ($title)
                <p class="font-bold mb-0.5">{{ $title }}</p>
            @endif

            @if ($message)
                <p>{{ $message }}</p>
            @endif

            @if ($errors)
                <ul class="list-disc list-inside mt-1 space-y-0.5">
                    @foreach ($errors as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>