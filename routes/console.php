<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Scheduled commands
|--------------------------------------------------------------------------
| Thanh toán online ở trạng thái pending phải được hết hạn tự động để
| hoàn tồn kho đúng nghiệp vụ. Lệnh payments:expire nằm trong
| app/Console/Commands/ExpirePendingOnlinePayments.php.
*/
Schedule::command('payments:expire')
    ->everyMinute()
    ->withoutOverlapping();
