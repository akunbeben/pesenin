@php
    use \Illuminate\Support\Number;
    use Laravel\Pennant\Feature;
    use Filament\Facades\Filament;
@endphp

<div
    class="min-h-screen px-2 mx-auto overflow-y-auto subpixel-antialiased sm:max-w-sm md:max-w-3xl"
    style="scrollbar-gutter: stable;"
    x-data="{ scrolling: false }"
    x-init="() => {
        $refs.root.addEventListener('scroll', () => {
            scrolling = true;
            setTimeout(() => {
                scrolling = false;
            }, 1000);
        });
    }"
    x-ref="root"
>
    @include('components.browse.cart')
    @include('components.browse.grid')
    @include('components.browse.cart-modal')
    @include('components.browse.product-modal')
    @include('components.browse.payment-method')
</div>
