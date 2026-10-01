<?php

namespace App\Services\Checkout;

use App\Models\CayCanh;
use App\Models\DiaChi;
use App\Models\GioHang;
use App\Models\Voucher;
use App\Services\MapShippingService;

class CheckoutPageService
{
    public function __construct(
        private MapShippingService $map,
        private ShippingPricingService $shippingPricing,
        private CheckoutIntegrityService $checkoutIntegrity,
    ) {}

    public function dataFor($user): array
    {
        $buyNow = session('greenshop_buy_now_' . $user->user_id);
        if (request()->query('cart') === '1') {
            session()->forget('greenshop_buy_now_' . $user->user_id);
            $buyNow = null;
        }
        $buyNow = is_array($buyNow) ? $buyNow : null;
        if ($buyNow) {
            $plant = CayCanh::find((int) ($buyNow['plant_id'] ?? 0));
            if (!$plant || $plant->trang_thai !== 'Đang bán' || (int) $plant->so_luong < (int) ($buyNow['quantity'] ?? 0) || (int) ($buyNow['quantity'] ?? 0) < 1) {
                throw new \RuntimeException('EMPTY_CART');
            }
            $detail = (object) ['plant_id' => $plant->plant_id, 'so_luong' => (int) $buyNow['quantity'], 'cayCanh' => $plant];
            $cart = (object) ['chiTietGioHangs' => collect([$detail])];
        } else {
            $cart = GioHang::where('user_id', $user->user_id)->with('chiTietGioHangs.cayCanh')->first();
        }

        if (!$cart || $cart->chiTietGioHangs->isEmpty()) {
            throw new \RuntimeException('EMPTY_CART');
        }

        $addresses = DiaChi::where('user_id', $user->user_id)
            ->orderByDesc('mac_dinh')->orderBy('address_id')->get();
        $selectedAddress = $addresses->firstWhere('mac_dinh', 1) ?: $addresses->first();
        $oldAddressId = session()->getOldInput('address_id');
        if ($oldAddressId) {
            $selectedAddress = $addresses->firstWhere('address_id', (int) $oldAddressId) ?: $selectedAddress;
        }

        $subtotal = $cart->chiTietGioHangs->sum(fn ($detail) => ((float) optional($detail->cayCanh)->gia) * (int) $detail->so_luong);
        $shippingQuote = null;
        $shippingError = null;
        $shippingFee = 0.0;

        $nowLocal = now('Asia/Ho_Chi_Minh')->format('Y-m-d H:i:s');
        $allVouchers = Voucher::query()
            ->where('trang_thai', 1)
            ->where('ngay_bat_dau', '<=', $nowLocal)
            ->where('ngay_ket_thuc', '>=', $nowLocal)
            ->whereColumn('da_su_dung', '<', 'so_luong')
            ->orderBy('don_hang_toi_thieu')->orderByDesc('gia_tri_giam')->get();

        $vouchers = $allVouchers->where('pham_vi', 'don_hang')->values();
        $shippingVouchers = $allVouchers->where('pham_vi', 'van_chuyen')->values();

        $hasOldVoucherInput = session()->hasOldInput('voucher_id');
        $oldVoucherId = (int) session()->getOldInput('voucher_id', 0);
        $selectedVoucher = $hasOldVoucherInput
            ? ($oldVoucherId > 0 ? $vouchers->firstWhere('voucher_id', $oldVoucherId) : null)
            : $vouchers->filter(fn ($voucher) => (float) $subtotal >= (float) $voucher->don_hang_toi_thieu)
                ->sortByDesc(fn ($voucher) => (float) $voucher->tinhTienGiam((float) $subtotal))->first();
        $voucherDiscount = $selectedVoucher ? (float) $selectedVoucher->tinhTienGiam((float) $subtotal) : 0.0;
        if ($voucherDiscount <= 0) $selectedVoucher = null;

        $cartPlantIds = $cart->chiTietGioHangs->pluck('plant_id')->filter()->map(fn ($id) => (int) $id)->values()->all();
 
        $cartCategoryIds = $cart->chiTietGioHangs->pluck('cayCanh.category_id')
            ->filter()->unique()->map(fn ($id) => (int) $id)->values()->all();
        $suggestedQuery = CayCanh::query()
            ->when(!empty($cartPlantIds), fn ($q) => $q->whereNotIn('plant_id', $cartPlantIds))
            ->where('so_luong', '>', 0)->where('trang_thai', 'Đang bán');
        if ($cartCategoryIds) {
            $placeholders = implode(',', array_fill(0, count($cartCategoryIds), '?'));
            $suggestedQuery->orderByRaw("CASE WHEN category_id IN ($placeholders) THEN 0 ELSE 1 END", $cartCategoryIds);
        }
        $suggestedProducts = $suggestedQuery->orderBy('gia')->limit(40)
            ->get(['plant_id','category_id','ten_cay','gia','anh_dai_dien']);

        $selectedShippingMethod = strtoupper((string) session()->getOldInput('shipping_method', 'STANDARD'));
        if (!in_array($selectedShippingMethod, ['STANDARD','EXPRESS','GHTK_EXPRESS'], true)) {
            $selectedShippingMethod = 'STANDARD';
        }

        $shippingVoucherId = null;
        $shippingVoucherDiscount = 0.0;
        if ($selectedAddress) {
            try {
                $shippingQuote = $this->map->quoteForAddress($selectedAddress);
                $distanceKm = (float) ($shippingQuote['distance_km'] ?? 0);
                $shippingFee = $this->shippingPricing->feeForMethod($selectedShippingMethod, $distanceKm, $this->map);
                $oldShippingVoucherId = (int) session()->getOldInput('shipping_voucher_id', 0);
                $best = session()->hasOldInput('shipping_voucher_id')
                    ? ($oldShippingVoucherId > 0 ? $shippingVouchers->firstWhere('voucher_id', $oldShippingVoucherId) : null)
                    : $shippingVouchers->filter(fn ($voucher) => $subtotal >= (float) $voucher->don_hang_toi_thieu)
                        ->sortByDesc(fn ($voucher) => (float) $voucher->tinhTienGiamPhiShip($shippingFee, $subtotal))->first();
                if ($best && $shippingFee > 0) {
                    $shippingVoucherDiscount = (float) $best->tinhTienGiamPhiShip($shippingFee, $subtotal);
                    if ($shippingVoucherDiscount > 0) $shippingVoucherId = (int) $best->voucher_id;
                }
            } catch (\Throwable $e) {
                $shippingError = $e->getMessage();
            }
        }

        $total = max(0, $subtotal + $shippingFee - $voucherDiscount - $shippingVoucherDiscount);
        $cartSignature = $this->checkoutIntegrity->cartSignature($cart->chiTietGioHangs);
        $sessionKey = 'greenshop_checkout_order_code_' . $user->user_id;
        $checkoutOrderCode = session($sessionKey);
        if (!$checkoutOrderCode || !preg_match('/^GS\d{6}-\d{4}$/', (string) $checkoutOrderCode)) {
            $checkoutOrderCode = $this->checkoutIntegrity->generateOrderCode();
            session([$sessionKey => $checkoutOrderCode]);
        }

        return compact(
            'cart','addresses','selectedAddress','subtotal','shippingFee','total','cartSignature','shippingQuote','shippingError',
            'checkoutOrderCode','vouchers','selectedVoucher','voucherDiscount','suggestedProducts','selectedShippingMethod',
            'shippingVouchers','shippingVoucherId','shippingVoucherDiscount','cartCategoryIds','buyNow'
        );
    }
}
