<?php

namespace App\Services\Checkout;

use App\Models\CayCanh;
use App\Models\ChiTietDonHang;
use App\Models\DonHang;
use App\Models\GiaoDichThanhToan;
use App\Models\GioHang;
use App\Models\ThanhToan;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderCreationService
{
    public function __construct(
        private readonly CheckoutIntegrityService $checkoutIntegrity,
    ) {
    }

    /**
     * Tạo đơn hàng và các bản ghi liên quan trong một transaction.
     *
     * @return array{0: DonHang, 1: GiaoDichThanhToan|null}
     */
    public function create(
        object $user,
        object $address,
        float $shippingFee,
        string $shippingMethod,
        string $paymentMethod,
        string $checkoutOrderCode,
        string $cartSignature,
        ?int $voucherId,
        ?int $shippingVoucherId,
        ?array $buyNow = null,
    ): array {
        return DB::transaction(function () use (
            $user,
            $address,
            $shippingFee,
            $shippingMethod,
            $paymentMethod,
            $checkoutOrderCode,
            $cartSignature,
            $voucherId,
            $shippingVoucherId,
            $buyNow,
        ) {
            if ($buyNow) {
                $details = collect([(object) [
                    'plant_id' => (int) $buyNow['plant_id'],
                    'so_luong' => (int) $buyNow['quantity'],
                ]]);
            } else {
                $cart = GioHang::where('user_id', $user->user_id)->lockForUpdate()->first();
                if (!$cart) throw new \RuntimeException('EMPTY_CART');
                $details = DB::table('chi_tiet_gio_hang')->where('cart_id', $cart->cart_id)->lockForUpdate()->get();
                if ($details->isEmpty()) throw new \RuntimeException('EMPTY_CART');
            }

            $plants = CayCanh::whereIn('plant_id', $details->pluck('plant_id')->all())
                ->lockForUpdate()
                ->get()
                ->keyBy('plant_id');

            $signatureItems = collect();
            $subtotal = 0.0;

            foreach ($details as $detail) {
                $plant = $plants->get($detail->plant_id);
                if (!$plant) {
                    throw new \RuntimeException('PRODUCT_MISSING:' . $detail->plant_id);
                }

                $status = strtolower(trim((string) $plant->trang_thai));
                if ($plant->trang_thai !== 'Đang bán') {
                    throw new \RuntimeException('PRODUCT_HIDDEN:' . $plant->ten_cay);
                }

                $quantity = (int) $detail->so_luong;
                $stock = (int) $plant->so_luong;
                if ($stock <= 0) {
                    throw new \RuntimeException('OUT_OF_STOCK:' . $plant->ten_cay);
                }
                if ($quantity > $stock) {
                    throw new \RuntimeException('LOW_STOCK:' . $plant->ten_cay . '|' . $stock);
                }

                $price = (float) $plant->gia;
                $subtotal += $price * $quantity;
                $signatureItems->push((object) [
                    'plant_id' => $plant->plant_id,
                    'so_luong' => $quantity,
                    'cayCanh' => $plant,
                ]);
            }

            $currentSignature = $this->checkoutIntegrity->cartSignature($signatureItems);
            if (!hash_equals($cartSignature, $currentSignature)) {
                throw new \RuntimeException('CART_CHANGED');
            }

            $voucher = null;
            $voucherDiscount = 0.0;

            if (($voucherId ?? 0) > 0) {
                $voucher = Voucher::where('voucher_id', $voucherId)
                    ->where('pham_vi', 'don_hang')
                    ->lockForUpdate()
                    ->first();

                if (!$voucher) {
                    throw new \RuntimeException('VOUCHER_MISSING');
                }

                if (!$voucher->dangCoHieuLuc()) {
                    throw new \RuntimeException('VOUCHER_INVALID');
                }

                if ($subtotal < (float) $voucher->don_hang_toi_thieu) {
                    throw new \RuntimeException(
                        'VOUCHER_MIN:' . max(0, (float) $voucher->don_hang_toi_thieu - $subtotal)
                    );
                }

                $voucherDiscount = (float) $voucher->tinhTienGiam($subtotal);
            }

            $shippingVoucher = null;
            $shippingVoucherDiscount = 0.0;

            if (($shippingVoucherId ?? 0) > 0) {
                $shippingVoucher = Voucher::where('voucher_id', $shippingVoucherId)
                    ->where('pham_vi', 'van_chuyen')
                    ->lockForUpdate()
                    ->first();

                if (!$shippingVoucher || !$shippingVoucher->dangCoHieuLuc()) {
                    throw new \RuntimeException('VOUCHER_SHIP_INVALID');
                }

                $shippingVoucherDiscount = (float) $shippingVoucher->tinhTienGiamPhiShip($shippingFee, $subtotal);
                if ($shippingVoucherDiscount <= 0) {
                    throw new \RuntimeException('VOUCHER_SHIP_MIN');
                }
            }

            $total = max(0, $subtotal + $shippingFee - $voucherDiscount - $shippingVoucherDiscount);

            if (DonHang::where('order_code', $checkoutOrderCode)->exists()) {
                throw new \RuntimeException('ORDER_CODE_USED');
            }

            $order = DonHang::create([
                'order_code' => $checkoutOrderCode,
                'user_id' => $user->user_id,
                'address_id' => $address->address_id,
                'tong_tien' => $total,
                'phi_van_chuyen' => $shippingFee,
                'phuong_thuc_van_chuyen' => $shippingMethod,
                'ma_voucher_van_chuyen' => $shippingVoucher?->ma_voucher,
                'giam_phi_van_chuyen' => $shippingVoucherDiscount,
                'voucher_id' => $voucher?->voucher_id,
                'tien_giam' => $voucherDiscount,
                'trang_thai' => in_array($paymentMethod, ['PAYPAL', 'PAYOS'], true)
                    ? 'pending_payment'
                    : 'pending_confirmation',
                'ngay_dat' => now(),
            ]);

            foreach ($details as $detail) {
                $plant = $plants->get($detail->plant_id);
                $quantity = (int) $detail->so_luong;

                ChiTietDonHang::create([
                    'order_id' => $order->order_id,
                    'plant_id' => $plant->plant_id,
                    'don_gia' => $plant->gia,
                    'so_luong' => $quantity,
                ]);

                $plant->so_luong = (int) $plant->so_luong - $quantity;
                $plant->save();
            }

            $payment = ThanhToan::create([
                'order_id' => $order->order_id,
                'phuong_thuc' => $paymentMethod,
                'so_tien' => $total,
                'trang_thai' => 'pending',
                'ngay_thanh_toan' => null,
            ]);

            $onlineTransaction = null;

            if (in_array($paymentMethod, ['PAYPAL', 'PAYOS'], true)) {
                $onlineTransaction = GiaoDichThanhToan::create([
                    'payment_id' => $payment->payment_id,
                    'provider' => $paymentMethod,
                    'merchant_transaction_id' => $checkoutOrderCode,
                    'provider_request_id' => (string) Str::uuid(),
                    'provider_transaction_id' => null,
                    'amount' => $total,
                    'status' => 'pending',
                    'response_code' => null,
                    'response_message' => null,
                    'payment_url' => null,
                    'expires_at' => now()->addMinutes(
                        max(5, (int) config('services.payments.ttl_minutes', 15))
                    ),
                    'paid_at' => null,
                ]);
            }

            if ($voucher) {
                $voucher->da_su_dung = (int) $voucher->da_su_dung + 1;
                $voucher->save();
            }

            if ($shippingVoucher) {
                $shippingVoucher->da_su_dung = (int) $shippingVoucher->da_su_dung + 1;
                $shippingVoucher->save();
            }

            if (!$buyNow) {
                DB::table('chi_tiet_gio_hang')->where('cart_id', $cart->cart_id)->delete();
            }

            return [$order, $onlineTransaction];
        }, 3);
    }
}
