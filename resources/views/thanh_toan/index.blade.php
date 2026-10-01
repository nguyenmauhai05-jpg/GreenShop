<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - GreenShop</title>
    @vite(['resources/css/customer/site-shell.css'])
    @vite('resources/css/customer/thanh-toan.css')
</head>
<body>
@include('trang_chu.components.header')

@php
    $selectedPaymentMethod = in_array(old('payment_method', 'COD'), ['COD', 'PAYPAL', 'PAYOS'], true)
        ? old('payment_method', 'COD') : 'COD';
    $selectedShippingMethod = old('shipping_method', $selectedShippingMethod ?? 'STANDARD');
    $distanceKm = (float) ($shippingQuote['distance_km'] ?? 0);

    $standardShippingFee = $distanceKm <= 5
        ? 15000
        : ($distanceKm <= 10 ? 20000 : ($distanceKm <= 20 ? 30000 : min(50000, 30000 + (ceil(($distanceKm - 20) / 10) * 5000))));
    $expressShippingFee = (float) ($shippingQuote['shipping_fee'] ?? 0);
    $ghtkShippingFee = $distanceKm <= 5
        ? 18000
        : ($distanceKm <= 10 ? 25000 : min(60000, 25000 + (ceil(($distanceKm - 10) / 10) * 7000)));

    $shippingFee = match ($selectedShippingMethod) {
        'EXPRESS' => $expressShippingFee,
        'GHTK_EXPRESS' => $ghtkShippingFee,
        default => $standardShippingFee,
    };

    $shippingVoucherDiscount = min((float)($shippingVoucherDiscount ?? 0), $shippingFee);
    $total = max(0, (float)$subtotal + $shippingFee - (float)$voucherDiscount - $shippingVoucherDiscount);
    $standardFrom = now()->addDay()->format('d/m');
    $standardTo = now()->addDays(4)->format('d/m');
@endphp

<main class="checkout-page">
    <div class="checkout-heading">
        <div>
            <span class="checkout-kicker">GREENSHOP CHECKOUT</span>
            <h1>Thanh toán</h1>
            <p>Kiểm tra thông tin giao hàng và đơn hàng trước khi xác nhận.</p>
        </div>
        <a href="{{ route('gio-hang') }}" class="back-link">← Quay lại giỏ hàng</a>
    </div>

    @if(session('error') || session('warning'))
        <div class="checkout-alert {{ session('error') ? 'error' : 'warning' }}">
            <span>!</span><div>{{ session('error') ?? session('warning') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="checkout-alert error">
            <span>!</span><div>{{ $errors->first() }}</div>
        </div>
    @endif

    <form method="POST" action="{{ route('thanh-toan.place-order') }}" id="checkoutForm">
        @csrf
        <input type="hidden" name="address_id" id="selectedAddressId" value="{{ $selectedAddress?->address_id }}">
        <input type="hidden" name="cart_signature" id="checkoutCartSignature" value="{{ $cartSignature }}">
        <input type="hidden" name="checkout_order_code" value="{{ $checkoutOrderCode }}">
        <input type="hidden" name="voucher_id" id="selectedVoucherId" value="{{ $selectedVoucher?->voucher_id }}">
        <input type="hidden" name="shipping_method" id="selectedShippingMethod" value="{{ $selectedShippingMethod }}">
        <input type="hidden" name="shipping_voucher_id" id="selectedShippingVoucherId" value="{{ $shippingVoucherId }}">

        <div class="checkout-grid">
            <section class="checkout-content">
                @include('thanh_toan.components.address')
                @include('thanh_toan.components.products')
                @include('thanh_toan.components.vouchers')
                @include('thanh_toan.components.shipping')
                @include('thanh_toan.components.payment')
            </section>
            @include('thanh_toan.components.summary')
        </div>
    </form>
</main>


@include('tai_khoan.components.address-modal', ['checkoutAddressModal' => true])

@php
    $checkoutVoucherPayload = $vouchers->map(function ($v) {
        return [
            'voucher_id' => $v->voucher_id,
            'ma_voucher' => $v->ma_voucher,
            'ten_voucher' => $v->ten_voucher,
            'loai_giam' => $v->loai_giam,
            'gia_tri_giam' => (float) $v->gia_tri_giam,
            'giam_toi_da' => $v->giam_toi_da !== null ? (float) $v->giam_toi_da : null,
            'don_hang_toi_thieu' => (float) $v->don_hang_toi_thieu,
        ];
    })->values();

    $checkoutSuggestedProductsPayload = $suggestedProducts->map(function ($p) {
        $imageUrl = null;

        if (!empty($p->anh_dai_dien)) {
            $imageUrl = (
                str_starts_with($p->anh_dai_dien, 'http://')
                || str_starts_with($p->anh_dai_dien, 'https://')
            )
                ? $p->anh_dai_dien
                : asset(ltrim($p->anh_dai_dien, '/'));
        }

        return [
            'plant_id' => $p->plant_id,
            'name' => $p->ten_cay,
            'price' => (float) $p->gia,
            'category_id' => (int) $p->category_id,
            'image' => $imageUrl,
            'url' => route('chi-tiet-cay', $p->plant_id),
        ];
    })->values();

    $checkoutShippingVoucherPayload = $shippingVouchers->map(function ($v) {
        return [
            'voucher_id' => $v->voucher_id,
            'ma_voucher' => $v->ma_voucher,
            'ten_voucher' => $v->ten_voucher,
            'don_hang_toi_thieu' => (float) $v->don_hang_toi_thieu,
            'loai_giam' => $v->loai_giam,
            'gia_tri_giam' => (float) $v->gia_tri_giam,
            'giam_toi_da' => $v->giam_toi_da !== null ? (float) $v->giam_toi_da : null,
        ];
    })->values();
@endphp


@php
    $checkoutOldAddressPayload = [
        'address_id' => old('address_id'),
        'address_mode' => old('address_mode', 'manual'),
        'nguoi_nhan' => old('nguoi_nhan'),
        'so_dien_thoai' => old('so_dien_thoai'),
        'tinh_thanh' => old('tinh_thanh'),
        'phuong_xa' => old('phuong_xa'),
        'dia_chi' => old('dia_chi'),
        'latitude' => old('latitude'),
        'longitude' => old('longitude'),
        'mac_dinh' => old('mac_dinh'),
    ];
@endphp

<script>
window.GreenShopAddressBook = {
    oldHasErrors: {{ old('address_edit_context') === 'checkout' && $errors->any() ? 'true' : 'false' }},
    createUrl: {{ Illuminate\Support\Js::from(url('/tai-khoan/so-dia-chi')) }},
    pageUrl: {{ Illuminate\Support\Js::from(url('/thanh-toan')) }},
    defaultRecipient: {{ Illuminate\Support\Js::from(Auth::user()->ho_ten ?? '') }},
    defaultPhone: {{ Illuminate\Support\Js::from(Auth::user()->so_dien_thoai ?? '') }},
    administrativeApiBase: 'https://provinces.open-api.vn/api/v2',
    old: {{ Illuminate\Support\Js::from($checkoutOldAddressPayload) }},
};
</script>

<script>
window.GreenShopCheckout = {
    subtotal: {{ (float) $subtotal }},
    quantityUrl: {{ Illuminate\Support\Js::from(route('thanh-toan.quantity')) }},
    buyNow: {{ $buyNow ? 'true' : 'false' }},
    cartCategoryIds: {{ Illuminate\Support\Js::from($cartCategoryIds) }},
    addToCartUrl: {{ Illuminate\Support\Js::from(route('gio-hang.them')) }},
    refreshCartUrl: {{ Illuminate\Support\Js::from(route('thanh-toan.cart-refresh')) }},
    initialShippingMethod: {{ Illuminate\Support\Js::from($selectedShippingMethod) }},
    initialShippingError: {{ Illuminate\Support\Js::from($shippingError) }},
    shippingQuoteUrl: {{ Illuminate\Support\Js::from(route('map.shipping-quote')) }},
    initialShippingQuote: {{ Illuminate\Support\Js::from($shippingQuote) }},
    initialStandardShippingFee: {{ (float) $standardShippingFee }},
    initialExpressShippingFee: {{ (float) $expressShippingFee }},
    initialGhtkShippingFee: {{ (float) $ghtkShippingFee }},
    shippingVouchers: {{ Illuminate\Support\Js::from($checkoutShippingVoucherPayload) }},
    initialShippingVoucherId: {{ Illuminate\Support\Js::from($shippingVoucherId) }},
    initialShippingVoucherDiscount: {{ (float) $shippingVoucherDiscount }},
    selectedAddressId: {{ Illuminate\Support\Js::from($selectedAddress?->address_id) }},
    initialVoucherId: {{ Illuminate\Support\Js::from($selectedVoucher?->voucher_id) }},
    initialVoucherDiscount: {{ (float) $voucherDiscount }},
    vouchers: {{ Illuminate\Support\Js::from($checkoutVoucherPayload) }},
    suggestedProducts: {{ Illuminate\Support\Js::from($checkoutSuggestedProductsPayload) }},
};
</script>
@vite('resources/js/customer/so-dia-chi.js')
    @vite('resources/js/customer/thanh-toan.js')
</body>
</html>
