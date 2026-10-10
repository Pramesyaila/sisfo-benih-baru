@props([
    'eyebrow' => null,
    'title',
    'description',
    'actionLabel' => null,
    'actionUrl' => null,
    'icon' => 'sprout',
])

<div class="empty-state">
    <div class="empty-state__icon"><x-customer.icon :name="$icon" :size="23"></x-customer.icon></div>
    @if ($eyebrow)
        <span class="empty-state__eyebrow">{{ $eyebrow }}</span>
    @endif
    <h2 class="empty-state__title">{{ $title }}</h2>
    <p class="empty-state__description">{{ $description }}</p>
    @if ($actionLabel && $actionUrl)
        <a class="btn btn--primary" href="{{ $actionUrl }}">
            {{ $actionLabel }}
            <x-customer.icon name="arrow-right" :size="15"></x-customer.icon>
        </a>
    @endif
</div>
