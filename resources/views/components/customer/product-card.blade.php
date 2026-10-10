@props(['product'])

<article class="product-card">
    <a class="product-card__link" href="{{ route('catalog.show', $product) }}">
        <x-customer.product-art :product="$product"></x-customer.product-art>
        <div class="product-card__body">
            <div class="product-card__topline">
                <span class="product-card__category">{{ $product->category->name }}</span>
                @if ($product->stock > 0)
                    <span class="availability {{ $product->isLowStock() ? 'availability--low' : 'availability--good' }}">
                        {{ $product->isLowStock() ? 'Stok menipis' : 'Tersedia' }}
                    </span>
                @else
                    <span class="availability availability--empty">Stok habis</span>
                @endif
            </div>
            <h2 class="product-card__name">{{ $product->name }}</h2>
            <p class="product-card__description">{{ $product->packagingLabel() }} &middot; {{ $product->stock }} {{ $product->packaging_unit }} tersedia</p>
            <div class="product-card__footer">
                <span class="product-card__price">{{ $product->formattedPrice() }}</span>
                <span class="product-card__arrow"><x-customer.icon name="arrow-right" :size="18"></x-customer.icon></span>
            </div>
        </div>
    </a>
</article>
