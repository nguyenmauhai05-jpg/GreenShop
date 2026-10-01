# Automated tests - MySQL only

GD6 bỏ SQLite khỏi cấu hình PHPUnit. Test sử dụng schema MySQL riêng `GreenShop_test`.

Tạo một lần trong MySQL Workbench:

```sql
CREATE DATABASE IF NOT EXISTS GreenShop_test
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

`phpunit.xml` trỏ tới `GreenShop_test`. Một số test tự tạo/xóa các bảng test bằng Laravel Schema Builder bên trong **database test**. Đây chỉ là test isolation, không phải cơ chế quản lý schema của GreenShop production.

Không đổi `DB_DATABASE` của PHPUnit thành `GreenShop`.

Sau khi đã có `vendor/`, chạy:

```bash
php artisan test
```
