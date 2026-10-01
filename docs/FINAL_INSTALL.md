# Cách dùng bản GreenShop FINAL

## 1. Giải nén source
Không ghi đè `.env` đang chạy của bạn nếu file đó chứa cấu hình MySQL/MoMo/Gemini đúng.

Nếu cài mới, copy `.env.example` thành `.env` rồi điền credential cục bộ.

## 2. Database
Database được quản lý bằng **MySQL Workbench**.

- Không dùng migration.
- Không dùng phpMyAdmin như một phần kiến trúc.
- Chạy/đọc `database/greenshop_required_updates.sql`.
- `database/optional_legacy_cleanup.sql` chỉ là tùy chọn; không chạy DROP nếu chưa backup/xác nhận.

## 3. Cài dependency

```bash
composer install
npm install
```

Nếu cài mới và `.env` chưa có `APP_KEY`:

```bash
php artisan key:generate
```

## 4. Xóa cache và build frontend

```bash
php artisan optimize:clear
npm run build
```

## 5. Chạy local
Theo cách bạn đang chạy XAMPP/Apache hoặc:

```bash
php artisan serve
```

## 6. Lưu ý storage
Nếu project cần public storage symlink và máy chưa có:

```bash
php artisan storage:link
```

ZIP source FINAL không đóng kèm ảnh chat AI runtime và không đóng `.env` thật.
