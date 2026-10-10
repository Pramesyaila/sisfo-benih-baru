@props(['product', 'size' => 'default'])

<div {{ $attributes->merge(['class' => 'product-art ' . ($size === 'large' ? 'product-art--large' : ($size === 'compact' ? 'product-art--compact' : ''))]) }}>
    @if ($product->image)
        <img class="product-art__image" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
    @else
        <div class="product-art__placeholder" aria-label="Ilustrasi produk {{ $product->name }}">
            <span class="product-art__sun"></span>
            <span class="product-art__soil"></span>
            <svg class="product-art__leaf" width="{{ $size === 'compact' ? 38 : 118 }}" height="{{ $size === 'compact' ? 38 : 118 }}" viewBox="0 0 120 120" fill="none" aria-hidden="true">
                <path d="M61 104V48" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path>
                <path d="M61 69C39 70 26 59 25 37c21-1 34 9 36 32Z" fill="currentColor" opacity=".82"></path>
                <path d="M61 57c1-22 14-34 36-35 0 22-13 34-36 35Z" fill="currentColor" opacity=".62"></path>
                <path d="M61 83c17 0 28-8 29-24-17-1-28 8-29 24Z" fill="currentColor" opacity=".72"></path>
                <path d="M42 105h38" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path>
            </svg>
            <span class="product-art__label">Benih pilihan</span>
        </div>
    @endif
</div>
