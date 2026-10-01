# GD6 Validation

Các kiểm tra tĩnh đã chạy sau khi refactor:

- PHP lint (`app/`, `routes/`, `config/`, `bootstrap/`): **109 file PASS**.
- JavaScript `node --check`: **14 file PASS**.
- Route -> Controller method: **0 lỗi**.
- `Route::view` -> Blade: **0 lỗi**.
- Literal `view()/@include/@extends/@component` -> Blade: **0 lỗi**.
- Vite: **36 inputs**, **53 resource refs**, **0 file thiếu**, **0 input thiếu**.
- Runtime `Schema::create/table/hasColumn/hasTable`: **0**.
- Non-MySQL DB connection config: **0**.
- Zero-byte PHP/Blade source: **0**.
- Old UI font / Google Fonts ngoài PDF: **0**.
- Dump tables parsed: **24**.
- `DB::table()` literal trỏ tới bảng thiếu: **0**.
- Model `$table` trỏ tới bảng thiếu: **0**.
- Model `$fillable` chứa cột thiếu trong Dump: **0**.

## Chưa thể xác nhận trong gói clean

Gói refactor không đóng `vendor/` và `node_modules`, vì vậy môi trường hiện tại chưa chạy được đầy đủ:

```bash
php artisan route:list
php artisan view:cache
php artisan test
npm run build
```

Trên máy người dùng, sau khi `composer install` và `npm install`, nên chạy:

```bash
php artisan optimize:clear
npm run build
php artisan route:list
```

Nếu muốn chạy automated tests, tạo schema `GreenShop_test` theo `docs/TESTING_MYSQL.md`, sau đó:

```bash
php artisan test
```

Không chạy test trên database `GreenShop` thật.
