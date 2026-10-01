# GreenShop FINAL - Validation

Ngày đóng gói: 2026-08-30

## Kiểm tra đã chạy trong môi trường refactor

| Kiểm tra | Kết quả |
|---|---:|
| PHP lint (`app`, `routes`, `config`, `bootstrap`) | 113 file, 0 lỗi |
| JavaScript `node --check` (`resources/js`) | 19 file, 0 lỗi |
| Runtime `Schema::create/table/hasColumn/hasTable` | 0 reference |
| Font UI cũ / Google Fonts (ngoại trừ PDF) | 0 reference |
| Non-MySQL connection trong `config/database.php` | 0 reference |
| PHP/Blade file 0 byte | 0 file |
| Vite input | 46 |
| Blade `@vite` reference | 67 |
| Vite reference thiếu file | 0 |
| Vite reference không có trong input | 0 |
| Vite input thiếu file | 0 |

Các lượt rà source trước khi đóng FINAL cũng đã kiểm tra route -> Controller method, Blade view reference, Model/table/column và `DB::table()` literal so với Dump; không phát hiện method/view/bảng/cột bắt buộc bị thiếu ở trạng thái FINAL.

## Kiểm tra không thể chạy đầy đủ trong container

Bản phân phối FINAL chủ động không chứa `vendor/` và `node_modules/`.

Vì vậy trong môi trường đóng gói này không xác nhận runtime bằng:
- `php artisan route:list`
- Blade compile thông qua Laravel runtime
- `npm run build`

Ngoài ra CLI hiện tại không có lệnh `composer`.

Các kiểm tra trên cần chạy trên máy người dùng sau `composer install` và `npm install`.

## Checklist runtime trên máy người dùng

```bash
composer install
npm install
php artisan optimize:clear
npm run build
php artisan route:list
```

Không chạy:

```bash
php artisan migrate
php artisan migrate:fresh
```

## Checklist nghiệp vụ nên test thủ công

- Đăng ký / đăng nhập / đăng xuất.
- Quên mật khẩu / đặt lại mật khẩu.
- Trang chủ, cửa hàng, tìm kiếm, filter.
- Chi tiết cây và đánh giá.
- Giỏ hàng: thêm, cập nhật, xóa, giới hạn tồn kho.
- Checkout: địa chỉ, voucher đơn hàng, voucher vận chuyển, phương thức giao hàng.
- COD / chuyển khoản / MoMo Sandbox / retry payment.
- Danh sách và chi tiết đơn hàng, xác nhận nhận hàng.
- Hồ sơ và sổ địa chỉ.
- AI chăm sóc cây và upload ảnh.
- Admin: Dashboard, cây cảnh, danh mục, đơn hàng, voucher, đánh giá, báo cáo, cài đặt.
