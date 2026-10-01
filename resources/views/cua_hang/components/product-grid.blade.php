<div class="products-grid">

    @foreach($cayCanhs as $cay)

        @include(
            'cua_hang.components.product-card',
            ['cay' => $cay]
        )

    @endforeach

</div>