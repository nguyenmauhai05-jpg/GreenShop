# GreenShop

Website thương mại điện tử bán cây cảnh xây dựng bằng **Laravel 12 + MySQL + Vite**.

## Yêu cầu môi trường

- PHP 8.2+
- Composer
- Node.js / npm
- MySQL
- MySQL Workbench để quản lý/import database

## Database

GreenShop thống nhất **chỉ dùng MySQL** cho database ứng dụng.

Nguồn schema/dữ liệu chuẩn là database MySQL hiện tại và file Dump mới nhất. Project **không dùng Laravel migration** để tạo/sửa schema production.

Không chạy:

```bash
php artisan migrate
php artisan migrate:fresh
```

Nếu source cần thêm bảng/cột/index/FK, xem và chạy thủ công bằng MySQL Workbench:

```text
database/greenshop_required_updates.sql
```

Chi tiết: `database/README_MYSQL.md`.

## Cài đặt source

```bash
composer install
npm install
```

Tạo `.env` từ `.env.example`, sau đó cấu hình MySQL:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=GreenShop
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

Tạo application key nếu chưa có:

```bash
php artisan key:generate
```

Xóa cache cấu hình và build frontend:

```bash
php artisan optimize:clear
npm run build
```

Chạy local:

```bash
php artisan serve
```

## Font giao diện

Toàn bộ UI khách hàng và Admin dùng:

```css
font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
```

PDF server-side có thể giữ `DejaVu Sans` để đảm bảo render tiếng Việt.

## Thanh toán online

Kiến trúc thanh toán nằm tại:

```text
app/Services/Payments/
```

Hướng dẫn cấu hình MoMo/ZaloPay Sandbox:

```text
docs/online-payments.md
MOMO_SETUP.md
```

Không commit khóa Merchant thật vào Git.

## Automated tests

Tests được cấu hình dùng **MySQL schema riêng `GreenShop_test`**, không dùng SQLite và không được chạy trên database `GreenShop` thật.

Xem:

```text
docs/TESTING_MYSQL.md
```

## Tài liệu refactor

```text
docs/REFACTOR_STATUS.md
docs/GD6_CHANGED_FILES.md
```
