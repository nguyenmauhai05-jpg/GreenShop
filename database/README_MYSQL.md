# GreenShop Database - MySQL only

Nguồn schema/dữ liệu chuẩn của project là **MySQL** được quản lý bằng **MySQL Workbench** và file Dump mới nhất.

Quy ước:

- Không dùng phpMyAdmin như một phần kiến trúc project.
- Không chạy `php artisan migrate` hoặc `migrate:fresh`.
- Không dùng Laravel migration để cập nhật schema production.
- Runtime source không được gọi `Schema::create()`, `Schema::table()`, `Schema::hasColumn()` để tự sửa DB.
- Nếu source cần bảng/cột/index/FK mới, câu SQL phải được ghi vào `database/greenshop_required_updates.sql` để chạy thủ công.
- `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync` nên ứng dụng không phụ thuộc bảng framework `sessions`, `cache`, `jobs`.
- Automated tests (nếu chạy) dùng **MySQL schema riêng `GreenShop_test`**, không dùng SQLite và không được trỏ vào DB `GreenShop` thật.
