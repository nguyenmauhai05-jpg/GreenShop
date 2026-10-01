<?php

namespace App\Services\Orders;

use App\Models\DonHang;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminOrderQueryService
{
    private const ORDER_FILTERS = ['all','pending_confirmation','waiting_payment','preparing','shipping','delivered','completed','cancelled'];

    public function pageData(Request $request): array
    {
        $filters = $this->filters($request);
        $orders = $this->filteredQuery($filters)
            ->with(['nguoiDung', 'thanhToan'])
            ->withCount('chiTietDonHangs')
            ->orderByDesc('ngay_dat')->orderByDesc('order_id')
            ->paginate(10)->withQueryString();

        $selectedOrder = null;
        $selectedId = (int) $request->query('selected', 0);
        if ($selectedId > 0) {
            $selectedOrder = $this->detail($selectedId);
        }
        if (!$selectedOrder && $orders->count() > 0) {
            $selectedOrder = $this->detail((int) $orders->first()->order_id);
        }

        return ['orders' => $orders, 'counts' => $this->statusCounts(), 'selectedOrder' => $selectedOrder, 'filters' => $filters];
    }

    public function exportOrders(Request $request)
    {
        $filters = $this->filters($request);
        return [$filters, $this->filteredQuery($filters)->with(['nguoiDung', 'thanhToan'])->orderByDesc('ngay_dat')->get()];
    }

    public function filters(Request $request): array
    {
        $orderStatus = (string) $request->query('order_status', 'all');
        if (!in_array($orderStatus, self::ORDER_FILTERS, true)) $orderStatus = 'all';
        $paymentStatus = (string) $request->query('payment_status', 'all');
        if (!in_array($paymentStatus, ['all','pending','paid','cancelled'], true)) $paymentStatus = 'all';
        $paymentMethod = strtoupper((string) $request->query('payment_method', 'all'));
        if (!in_array($paymentMethod, ['ALL','COD','BANK_TRANSFER','PAYPAL','PAYOS'], true)) $paymentMethod = 'ALL';

        return [
            'q' => mb_substr(trim((string) $request->query('q', '')), 0, 100),
            'order_status' => $orderStatus,
            'payment_status' => $paymentStatus,
            'payment_method' => $paymentMethod === 'ALL' ? 'all' : $paymentMethod,
            'date_from' => $this->validDate((string) $request->query('date_from', '')),
            'date_to' => $this->validDate((string) $request->query('date_to', '')),
        ];
    }

    public function emptyCounts(): array
    {
        return ['all'=>0,'pending_confirmation'=>0,'waiting_payment'=>0,'preparing'=>0,'shipping'=>0,'delivered'=>0,'completed'=>0,'cancelled'=>0];
    }

    public function paymentMethodLabel(?string $method): string
    {
        return match (strtoupper(trim((string) $method))) {
            'COD','CASH','' => 'Thanh toán khi nhận hàng',
            'BANK_TRANSFER','BANK','TRANSFER','CHUYEN_KHOAN' => 'Chuyển khoản ngân hàng',
            'PAYPAL' => 'PayPal',
            'PAYOS' => 'payOS (QR ngân hàng)',
            default => 'Chưa xác định',
        };
    }

    public function paymentStatusLabel(?string $status): string
    {
        return match ($this->paymentStatus($status)) {
            'paid' => 'Đã thanh toán',
            'cancelled' => 'Đã hủy',
            default => 'Chờ xác nhận thanh toán',
        };
    }

    public function paymentStatus(?string $status): string
    {
        $value = Str::of((string) $status)->trim()->lower()->ascii()->replace([' ', '-'], '_')->value();
        if (in_array($value, ['paid','da_thanh_toan'], true)) return 'paid';
        if (in_array($value, ['cancelled','canceled','da_huy'], true)) return 'cancelled';
        return 'pending';
    }

    public function isCod(?string $method): bool
    {
        return in_array(strtoupper(trim((string) $method)), ['COD','CASH'], true);
    }

    private function detail(int $id): ?DonHang
    {
        return DonHang::with(['nguoiDung','diaChi','thanhToan','chiTietDonHangs.cayCanh'])->find($id);
    }

    private function filteredQuery(array $filters): Builder
    {
        $query = DonHang::query();
        if ($filters['q'] !== '') {
            $q = $filters['q'];
            $digits = preg_replace('/\D+/', '', $q);
            $candidateId = null;
            if (preg_match('/(\d{1,10})\s*$/', $q, $matches)) $candidateId = (int) ltrim($matches[1], '0');
            $query->where(function (Builder $sub) use ($q, $digits, $candidateId) {
                $normalizedCode = ltrim(strtoupper($q), '#');
                $sub->where('order_code', 'like', '%' . $normalizedCode . '%');
                if ($candidateId) $sub->orWhere('order_id', $candidateId);
                $sub->orWhereHas('nguoiDung', function (Builder $user) use ($q, $digits) {
                    $user->where('ho_ten', 'like', '%' . $q . '%')->orWhere('email', 'like', '%' . $q . '%');
                    if ($digits !== '') $user->orWhere('so_dien_thoai', 'like', '%' . $digits . '%');
                });
            });
        }

        if ($filters['order_status'] !== 'all') {
            if ($filters['order_status'] === 'waiting_payment') {
                $query->whereIn('trang_thai', DonHang::databaseStatusAliases('pending_confirmation'))
                    ->whereHas('thanhToan', function (Builder $payment) {
                        $payment->whereRaw('UPPER(phuong_thuc) NOT IN (?, ?)', ['COD','CASH'])
                            ->whereNotIn('trang_thai', ['paid','da_thanh_toan','đã thanh toán']);
                    });
            } else {
                $query->whereIn('trang_thai', DonHang::databaseStatusAliases($filters['order_status']));
            }
        }

        if ($filters['payment_status'] !== 'all') {
            $aliases = match ($filters['payment_status']) {
                'paid' => ['paid','da_thanh_toan','đã thanh toán'],
                'pending' => ['pending','waiting_confirmation','cho_xac_nhan','chờ xác nhận'],
                'cancelled' => ['cancelled','canceled','da_huy','đã hủy'],
                default => [$filters['payment_status']],
            };
            $query->whereHas('thanhToan', fn (Builder $payment) => $payment->whereIn('trang_thai', $aliases));
        }

        if ($filters['payment_method'] !== 'all') {
            if ($filters['payment_method'] === 'COD') {
                $query->whereHas('thanhToan', fn (Builder $payment) => $payment->whereRaw('UPPER(phuong_thuc) IN (?, ?)', ['COD','CASH']));
            } else {
                $query->whereHas('thanhToan', fn (Builder $payment) => $payment->whereRaw('UPPER(phuong_thuc) NOT IN (?, ?)', ['COD','CASH']));
            }
        }
        if ($filters['date_from'] !== '') $query->whereDate('ngay_dat', '>=', $filters['date_from']);
        if ($filters['date_to'] !== '') $query->whereDate('ngay_dat', '<=', $filters['date_to']);
        return $query;
    }

    private function statusCounts(): array
    {
        $orders = DonHang::with('thanhToan')->get();
        $counts = $this->emptyCounts();
        $counts['all'] = $orders->count();
        foreach ($orders as $order) {
            $status = $order->normalizedStatus();
            if ($status === 'pending_confirmation') {
                if ($order->thanhToan && $this->isCod($order->thanhToan->phuong_thuc)) $counts['pending_confirmation']++;
                elseif ($order->thanhToan && $this->paymentStatus($order->thanhToan->trang_thai) !== 'paid') $counts['waiting_payment']++;
                else $counts['pending_confirmation']++;
                continue;
            }
            if (isset($counts[$status])) $counts[$status]++;
        }
        return $counts;
    }

    private function validDate(string $date): string
    {
        return ($date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) ? $date : '';
    }
}
