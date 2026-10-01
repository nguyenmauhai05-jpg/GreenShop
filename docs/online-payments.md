# Thanh toán online GreenShop

## Trạng thái hiện tại

Checkout GreenShop hiện đang cho người dùng chọn:

- `COD`
- `PAYOS` (QR tự động, xác nhận bằng webhook hợp lệ)
- `PAYPAL`


Source vẫn giữ `ZaloPayPaymentGateway` cùng các DTO/service dùng chung để có thể mở rộng sau này, nhưng **checkout và `routes/web.php` hiện chưa bật ZaloPay**. Vì vậy không xem ZaloPay là phương thức đang hoạt động trên giao diện hiện tại và không tự thêm route/UI trong đợt refactor này.

## 1. Database thanh toán - chỉ MySQL

GreenShop không dùng Laravel migration để tạo/sửa schema. Database chuẩn là MySQL được import/quản lý bằng MySQL Workbench. Hai bảng thanh toán online `thanh_toan` và `giao_dich_thanh_toan` đã có trong Dump hiện tại.

Nếu source cần thêm index/constraint/cột, chỉ chạy SQL trong:

```text
database/greenshop_required_updates.sql
```

Không chạy:

```text
php artisan migrate
php artisan migrate:fresh
```

## 2. Demo local

Demo local mô phỏng trang thanh toán và callback ngay trong GreenShop, không thu tiền thật. Chế độ này chỉ nên dùng ở `local`/`testing`:

```dotenv
APP_ENV=local
APP_URL=http://127.0.0.1:8000
PAYMENT_DEMO_MODE=true
```


```dotenv
PAYMENT_DEMO_MODE=false
```

Sau khi đổi `.env`:

```powershell
php artisan config:clear
```


Điền credential thật **chỉ trong `.env` trên máy**, không commit vào Git và không đưa vào file ZIP chia sẻ:

```dotenv
PAYOS_CLIENT_ID=
PAYOS_API_KEY=
PAYOS_CHECKSUM_KEY=
PAYPAL_CLIENT_ID=
PAYPAL_CLIENT_SECRET=
```

**Không** dùng `GREENSHOP_BANK_*` nữa: QR VietQR thủ công và nút xác nhận chuyển khoản đã được bỏ. Đổi tài khoản ngân hàng nhận tiền tại kênh thu của payOS, không đổi qua `.env` VietQR cũ.

Các route đang hoạt động:

- PayOS và PayPal return/webhook trong `routes/web.php` và nhóm route thanh toán.
- Retry giao dịch: `/thanh-toan/online/{transaction}/retry`

IPN là endpoint public và được miễn CSRF trong `bootstrap/app.php`, nhưng payload vẫn phải qua xác minh chữ ký trong payment gateway trước khi cập nhật đơn hàng.

Trang return không được xem là bằng chứng thanh toán; trạng thái thanh toán được xử lý qua callback/IPN đã xác minh.

## 4. ZaloPay - code mở rộng, chưa bật trên checkout

`PaymentGatewayManager` và `ZaloPayPaymentGateway` vẫn được giữ để tránh phá kiến trúc payment đa provider. `config/services.php` và `.env.example` đã có cấu hình tương ứng:

```dotenv
ZALOPAY_APP_ID=
ZALOPAY_KEY1=
ZALOPAY_KEY2=
ZALOPAY_BASE_URL=https://sb-openapi.zalopay.vn
ZALOPAY_CREATE_ENDPOINT=/v2/create
ZALOPAY_TIMEOUT=30
ZALOPAY_EXPIRE_DURATION_SECONDS=900
```

Tuy nhiên trước khi bật ZaloPay thật cần thực hiện đầy đủ cùng lúc:

1. thêm lựa chọn ZaloPay ở checkout;
2. cho phép `payment_method=ZALOPAY` ở validation/tạo đơn;
3. tạo transaction provider `ZALOPAY`;
4. khai báo callback/return route;
5. cấu hình CSRF exception cho callback server-to-server;
6. test chữ ký/MAC và regression payment.

Không nên chỉ thêm route riêng lẻ vì sẽ tạo trạng thái nửa hoạt động.

## 5. Hết hạn giao dịch online

Command:

```powershell
php artisan payments:expire
```

được schedule trong `routes/console.php` mỗi phút bằng `withoutOverlapping()`.

Môi trường phát triển:

```powershell
php artisan schedule:work
```

Trên server có thể cấu hình cron gọi:

```text
php artisan schedule:run
```

mỗi phút.

Tác vụ hết hạn gọi `PaymentLifecycleService` để xử lý transaction pending quá hạn và hoàn tồn kho theo nghiệp vụ hiện tại.

## 6. Kiểm tra nhanh

```powershell
php artisan route:list --name=thanh-toan
php artisan payments:expire
php artisan test tests/Unit/Services/Payments tests/Feature/Payments
```

Chỉ chạy automated test với database `GreenShop_test`, không chạy test trên database `GreenShop` thật.
