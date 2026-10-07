@props(['name' => 'leaf', 'size' => 20])

<svg
    {{ $attributes->merge(['class' => 'inline-flex shrink-0']) }}
    width="{{ $size }}"
    height="{{ $size }}"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="1.8"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
>
    @switch($name)
        @case('search')
            <circle cx="11" cy="11" r="6.5"></circle>
            <path d="m16 16 4.2 4.2"></path>
            @break
        @case('bag')
            <path d="M5.5 8.5h13l1 11h-15l1-11Z"></path>
            <path d="M9 9V6.8a3 3 0 0 1 6 0V9"></path>
            @break
        @case('clipboard')
            <rect x="5" y="4.5" width="14" height="16" rx="2"></rect>
            <path d="M9 4.5V3h6v1.5M8.5 10h7M8.5 14h5"></path>
            @break
        @case('user')
            <circle cx="12" cy="8" r="3.2"></circle>
            <path d="M5.5 20c.5-3.3 2.7-5 6.5-5s6 1.7 6.5 5"></path>
            @break
        @case('leaf')
            <path d="M19.5 4.5C11 4.7 5.3 8 5.3 14.1c0 2.9 2.1 5.1 5 5.1 5.8 0 8.9-5.7 9.2-14.7Z"></path>
            <path d="M4.5 20c2.5-4.1 5.3-6.6 9-8.4"></path>
            @break
        @case('sprout')
            <path d="M12 20V10"></path>
            <path d="M12 12c-4.7.2-7.2-1.8-7.4-6.1C8.7 5.6 11.6 7.2 12 12Z"></path>
            <path d="M12 10.2c.4-4.5 3-6.7 7.2-6.7.1 4.1-2.3 6.4-7.2 6.7Z"></path>
            <path d="M8.5 20h7"></path>
            @break
        @case('arrow-right')
            <path d="M4 12h15"></path>
            <path d="m13 6 6 6-6 6"></path>
            @break
        @case('chevron-right')
            <path d="m9 5 7 7-7 7"></path>
            @break
        @case('chevron-down')
            <path d="m5 9 7 7 7-7"></path>
            @break
        @case('menu')
            <path d="M4 7h16M4 12h16M4 17h16"></path>
            @break
        @case('x')
            <path d="m6 6 12 12M18 6 6 18"></path>
            @break
        @case('logout')
            <path d="M10 4H6.8A1.8 1.8 0 0 0 5 5.8v12.4A1.8 1.8 0 0 0 6.8 20H10"></path>
            <path d="M13 8.5 17.5 12 13 15.5M17 12H8"></path>
            @break
        @case('check')
            <path d="m5 12.5 4.2 4.2L19 7"></path>
            @break
        @case('plus')
            <path d="M12 5v14M5 12h14"></path>
            @break
        @case('upload')
            <path d="M12 15V4"></path>
            <path d="m8 8 4-4 4 4"></path>
            <path d="M5 14v4.5A1.5 1.5 0 0 0 6.5 20h11a1.5 1.5 0 0 0 1.5-1.5V14"></path>
            @break
        @case('download')
            <path d="M12 4v11"></path>
            <path d="m8 11 4 4 4-4"></path>
            <path d="M5 17.5V19a1.5 1.5 0 0 0 1.5 1.5h11A1.5 1.5 0 0 0 19 19v-1.5"></path>
            @break
        @case('bell')
            <path d="M18 15.5V10a6 6 0 1 0-12 0v5.5L4.5 18h15L18 15.5Z"></path>
            <path d="M9.5 20.5a2.5 2.5 0 0 0 5 0"></path>
            @break
        @case('truck')
            <path d="M3 7h10v9H3zM13 10h3.5l2.5 3v3h-6"></path>
            <circle cx="7" cy="18" r="1.6"></circle>
            <circle cx="16.5" cy="18" r="1.6"></circle>
            @break
        @case('tag')
            <path d="M4 10.5V5a1 1 0 0 1 1-1h5.5l9 9-6.5 6.5-9-9Z"></path>
            <circle cx="8" cy="8" r="1.2"></circle>
            @break
        @case('package')
            <path d="m4 7 8-4 8 4-8 4-8-4Z"></path>
            <path d="M4 7v10l8 4 8-4V7M12 11v10"></path>
            @break
        @case('shield')
            <path d="M12 3.5 19 6v5.2c0 4.2-2.8 7.7-7 9.3-4.2-1.6-7-5.1-7-9.3V6l7-2.5Z"></path>
            <path d="m9 12 2 2 4-4"></path>
            @break
        @case('file')
            <path d="M6 3.5h8l4 4V20H6V3.5Z"></path>
            <path d="M14 3.5V8h4M9 12h6M9 16h6"></path>
            @break
        @case('calendar')
            <rect x="4.5" y="5.5" width="15" height="14" rx="2"></rect>
            <path d="M8 3.5v4M16 3.5v4M4.5 9.5h15"></path>
            @break
        @case('wallet')
            <path d="M5 6.5h13a2 2 0 0 1 2 2v9H5a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2Z"></path>
            <path d="M3 8.5V6.8A2.8 2.8 0 0 1 5.8 4H17M16 12h4"></path>
            <circle cx="16" cy="12" r=".7" fill="currentColor" stroke="none"></circle>
            @break
        @case('info')
            <circle cx="12" cy="12" r="8.5"></circle>
            <path d="M12 11v5M12 8h.01"></path>
            @break
        @case('phone')
            <path d="M7 4.5 9.2 8 7.8 9.5c1.1 2.2 2.5 3.6 4.7 4.7l1.5-1.4 3.5 2.2-.8 3.1c-.2.7-.9 1.1-1.6 1C9.5 18.1 5.9 14.5 4.9 8.9c-.1-.7.3-1.4 1-1.6L9 7l-2-2.5Z"></path>
            @break
        @case('mail')
            <rect x="4" y="6" width="16" height="12" rx="2"></rect>
            <path d="m5 8 7 5 7-5"></path>
            @break
        @case('map-pin')
            <path d="M19 10c0 4.5-7 10-7 10S5 14.5 5 10a7 7 0 1 1 14 0Z"></path>
            <circle cx="12" cy="10" r="2.2"></circle>
            @break
        @case('clock')
            <circle cx="12" cy="12" r="8.5"></circle>
            <path d="M12 7v5l3.5 2"></path>
            @break
        @default
            <path d="M19.5 4.5C11 4.7 5.3 8 5.3 14.1c0 2.9 2.1 5.1 5 5.1 5.8 0 8.9-5.7 9.2-14.7Z"></path>
            <path d="M4.5 20c2.5-4.1 5.3-6.6 9-8.4"></path>
    @endswitch
</svg>
