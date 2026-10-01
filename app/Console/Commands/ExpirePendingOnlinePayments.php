<?php

namespace App\Console\Commands;

use App\Models\GiaoDichThanhToan;
use App\Services\Payments\PaymentLifecycleService;
use Illuminate\Console\Command;
use Throwable;

class ExpirePendingOnlinePayments extends Command
{
    protected $signature = 'payments:expire';

    protected $description = 'Hủy các phiên thanh toán online hết hạn và hoàn lại tồn kho';

    public function handle(PaymentLifecycleService $lifecycle): int
    {
        $expired = 0;
        $failed = 0;

        GiaoDichThanhToan::query()
            ->where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->orderBy('transaction_id')
            ->chunkById(100, function ($transactions) use ($lifecycle, &$expired, &$failed) {
                foreach ($transactions as $transaction) {
                    try {
                        if ($lifecycle->expirePending($transaction)) {
                            $expired++;
                        }
                    } catch (Throwable $exception) {
                        report($exception);
                        $failed++;
                    }
                }
            }, 'transaction_id', 'transaction_id');

        $this->info("Đã hết hạn {$expired} giao dịch; lỗi {$failed} giao dịch.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
