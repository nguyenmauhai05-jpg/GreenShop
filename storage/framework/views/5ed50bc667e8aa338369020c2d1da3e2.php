<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - GreenShop</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/customer/site-shell.css']); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/customer/thanh-toan.css'); ?>
</head>
<body>
<?php echo $__env->make('trang_chu.components.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php
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
?>

<main class="checkout-page">
    <div class="checkout-heading">
        <div>
            <span class="checkout-kicker">GREENSHOP CHECKOUT</span>
            <h1>Thanh toán</h1>
            <p>Kiểm tra thông tin giao hàng và đơn hàng trước khi xác nhận.</p>
        </div>
        <a href="<?php echo e(route('gio-hang')); ?>" class="back-link">← Quay lại giỏ hàng</a>
    </div>

    <?php if(session('error') || session('warning')): ?>
        <div class="checkout-alert <?php echo e(session('error') ? 'error' : 'warning'); ?>">
            <span>!</span><div><?php echo e(session('error') ?? session('warning')); ?></div>
        </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="checkout-alert error">
            <span>!</span><div><?php echo e($errors->first()); ?></div>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('thanh-toan.place-order')); ?>" id="checkoutForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="address_id" id="selectedAddressId" value="<?php echo e($selectedAddress?->address_id); ?>">
        <input type="hidden" name="cart_signature" id="checkoutCartSignature" value="<?php echo e($cartSignature); ?>">
        <input type="hidden" name="checkout_order_code" value="<?php echo e($checkoutOrderCode); ?>">
        <input type="hidden" name="voucher_id" id="selectedVoucherId" value="<?php echo e($selectedVoucher?->voucher_id); ?>">
        <input type="hidden" name="shipping_method" id="selectedShippingMethod" value="<?php echo e($selectedShippingMethod); ?>">
        <input type="hidden" name="shipping_voucher_id" id="selectedShippingVoucherId" value="<?php echo e($shippingVoucherId); ?>">

        <div class="checkout-grid">
            <section class="checkout-content">
                <?php echo $__env->make('thanh_toan.components.address', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('thanh_toan.components.products', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('thanh_toan.components.vouchers', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('thanh_toan.components.shipping', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('thanh_toan.components.payment', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </section>
            <?php echo $__env->make('thanh_toan.components.summary', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </form>
</main>


<?php echo $__env->make('tai_khoan.components.address-modal', ['checkoutAddressModal' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php
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
?>


<?php
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
?>

<script>
window.GreenShopAddressBook = {
    oldHasErrors: <?php echo e(old('address_edit_context') === 'checkout' && $errors->any() ? 'true' : 'false'); ?>,
    createUrl: <?php echo e(Illuminate\Support\Js::from(url('/tai-khoan/so-dia-chi'))); ?>,
    pageUrl: <?php echo e(Illuminate\Support\Js::from(url('/thanh-toan'))); ?>,
    defaultRecipient: <?php echo e(Illuminate\Support\Js::from(Auth::user()->ho_ten ?? '')); ?>,
    defaultPhone: <?php echo e(Illuminate\Support\Js::from(Auth::user()->so_dien_thoai ?? '')); ?>,
    administrativeApiBase: 'https://provinces.open-api.vn/api/v2',
    old: <?php echo e(Illuminate\Support\Js::from($checkoutOldAddressPayload)); ?>,
};
</script>

<script>
window.GreenShopCheckout = {
    subtotal: <?php echo e((float) $subtotal); ?>,
    quantityUrl: <?php echo e(Illuminate\Support\Js::from(route('thanh-toan.quantity'))); ?>,
    buyNow: <?php echo e($buyNow ? 'true' : 'false'); ?>,
    cartCategoryIds: <?php echo e(Illuminate\Support\Js::from($cartCategoryIds)); ?>,
    addToCartUrl: <?php echo e(Illuminate\Support\Js::from(route('gio-hang.them'))); ?>,
    refreshCartUrl: <?php echo e(Illuminate\Support\Js::from(route('thanh-toan.cart-refresh'))); ?>,
    initialShippingMethod: <?php echo e(Illuminate\Support\Js::from($selectedShippingMethod)); ?>,
    initialShippingError: <?php echo e(Illuminate\Support\Js::from($shippingError)); ?>,
    shippingQuoteUrl: <?php echo e(Illuminate\Support\Js::from(route('map.shipping-quote'))); ?>,
    initialShippingQuote: <?php echo e(Illuminate\Support\Js::from($shippingQuote)); ?>,
    initialStandardShippingFee: <?php echo e((float) $standardShippingFee); ?>,
    initialExpressShippingFee: <?php echo e((float) $expressShippingFee); ?>,
    initialGhtkShippingFee: <?php echo e((float) $ghtkShippingFee); ?>,
    shippingVouchers: <?php echo e(Illuminate\Support\Js::from($checkoutShippingVoucherPayload)); ?>,
    initialShippingVoucherId: <?php echo e(Illuminate\Support\Js::from($shippingVoucherId)); ?>,
    initialShippingVoucherDiscount: <?php echo e((float) $shippingVoucherDiscount); ?>,
    selectedAddressId: <?php echo e(Illuminate\Support\Js::from($selectedAddress?->address_id)); ?>,
    initialVoucherId: <?php echo e(Illuminate\Support\Js::from($selectedVoucher?->voucher_id)); ?>,
    initialVoucherDiscount: <?php echo e((float) $voucherDiscount); ?>,
    vouchers: <?php echo e(Illuminate\Support\Js::from($checkoutVoucherPayload)); ?>,
    suggestedProducts: <?php echo e(Illuminate\Support\Js::from($checkoutSuggestedProductsPayload)); ?>,
};
</script>
<?php echo app('Illuminate\Foundation\Vite')('resources/js/customer/so-dia-chi.js'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/customer/thanh-toan.js'); ?>
</body>
</html>
<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/thanh_toan/index.blade.php ENDPATH**/ ?>