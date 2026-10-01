@vite([
    'resources/css/app.css',
    'resources/css/customer/site-shell.css',
    'resources/css/cham-soc-cay.css',
    'resources/js/customer/cham-soc-cay.js',
])

@include('trang_chu.components.header')

<main
    class="ai-care-page"
    id="aiCarePage"
    data-chat-url="{{ route('cham-soc-cay.chat') }}"
    data-base-url="{{ route('cham-soc-cay') }}"
    data-cart-url="{{ route('gio-hang.them') }}"
    data-authenticated="{{ auth()->check() ? '1' : '0' }}"
>
    <div class="ai-care-layout">
        @include('cham_soc_cay.components.sidebar')
        @include('cham_soc_cay.components.chat-panel')
    </div>
</main>

@include('trang_chu.components.footer')
