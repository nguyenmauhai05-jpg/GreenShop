# GreenShop MoMo

Local demo:
PAYMENT_DEMO_MODE=true

Sandbox thật:
PAYMENT_DEMO_MODE=false
MOMO_PARTNER_CODE=...
MOMO_ACCESS_KEY=...
MOMO_SECRET_KEY=...
MOMO_BASE_URL=https://test-payment.momo.vn
MOMO_CREATE_ENDPOINT=/v2/gateway/api/create
MOMO_TIMEOUT=30

Sau khi sửa .env:
php artisan optimize:clear

IPN của MoMo cần URL HTTPS public. localhost/127.0.0.1 không nhận được callback từ server MoMo.
