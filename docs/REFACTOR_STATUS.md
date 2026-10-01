# GreenShop Refactor Status - FINAL

## Trạng thái

Bản FINAL được refactor từ `GreenShop(4).zip`, đối chiếu với `Dump(1).sql` và theo nguyên tắc **MySQL/MySQL Workbench là nguồn chuẩn duy nhất**.

- Không dùng Laravel migration để thay đổi production database.
- Không dùng `Schema::create()`, `Schema::table()`, `Schema::hasColumn()` hoặc `Schema::hasTable()` trong runtime source.
- Nếu MySQL cần bổ sung constraint/index thì dùng `database/greenshop_required_updates.sql`.
- Toàn bộ giao diện web dùng `"Segoe UI", Tahoma, Geneva, Verdana, sans-serif`; PDF server-side giữ `DejaVu Sans` để tránh lỗi font tiếng Việt.

## Những nhóm refactor đã hoàn thành

### 1. Route, quyền và cấu hình
- Chuẩn hóa route Admin dùng `auth + admin`.
- Loại route VNPAY cũ gọi method không tồn tại.
- Loại route Map bị khai báo trùng.
- MySQL là DB runtime duy nhất trong `config/database.php`.
- Session/cache/queue mặc định: `file/file/sync`, không phụ thuộc bảng Laravel `sessions/cache/jobs`.
- Scheduler `payments:expire` được cấu hình chạy định kỳ.

### 2. Controller -> Service
Đã giảm trách nhiệm Controller và tách nghiệp vụ sang Service cho các nhóm:
- Checkout/Order creation/Shipping.
- Cart.
- Plants/Admin cây cảnh.
- Reports.
- AI chăm sóc cây.
- Shop/catalog/filter.
- Account/profile/address book.
- Auth/password reset.
- Customer/Admin orders.
- Admin category.
- Admin dashboard.

### 3. Blade/CSS/JS/asset
- Các Blade lớn được tách thành component/partial theo chức năng.
- CSS/JS first-party được chuẩn hóa về `resources/` và quản lý bằng Vite.
- JS inline lớn của Header, Shop, Checkout, Cart, Auth, Account, Admin... đã được chuyển ra file riêng.
- Loại `public - Copy`, Vite hot/cache/build cũ và nhiều file legacy/copy đã xác minh không còn reference.
- Tách CSS Trang chủ thành site shell + các section nhỏ.

### 4. Database
Người dùng đã xác nhận chạy thành công:
- `uq_nguoi_dung_email`.
- `fk_don_hang_voucher`.
- `danh_gia.order_detail_id` -> `BIGINT NULL`.
- `fk_danh_gia_order_detail`.

Source FINAL không phát hiện thêm **bảng/cột nghiệp vụ bắt buộc** bị thiếu so với Dump đã đối chiếu.

Các constraint/index tối ưu còn lại và đồng bộ 18 đường dẫn ảnh cây mẫu nằm trong:
- `database/greenshop_required_updates.sql`

Các bảng legacy chỉ được đề xuất kiểm tra, không tự DROP:
- `database/optional_legacy_cleanup.sql`

### 5. Font
- UI khách + Admin: Segoe UI thống nhất.
- Không còn Google Fonts/font UI cũ ghi đè trong resources/public.
- PDF Admin giữ DejaVu Sans có chủ đích.

## Code legacy đã loại khỏi source FINAL sau khi kiểm tra reference
Ví dụ:
- `app/Models/BaiViet.php`
- `app/Models/HinhAnhCay.php`
- `app/Models/LichSuChatAI.php`
- `app/Models/LienHe.php`
- `app/Models/MaQR.php`
- `app/Services/ShippingService.php`
- các Controller/Request rỗng hoặc legacy không còn route/reference
- migrations/factory/seeder cũ không còn thuộc quy trình MySQL-only

Lưu ý: bảng `ma_qr` vẫn phải giữ vì source còn query trực tiếp; chỉ Model legacy `MaQR` bị loại.

## Không tự xóa dữ liệu người dùng
- Không DROP các bảng legacy trong DB.
- Không đổi ảnh cho `plant_id=79,80` vì file tương ứng không tồn tại trong source được cung cấp.
- File `.env` thật không được đóng vào ZIP FINAL.
- Ảnh chat AI runtime trong `storage/app/public/chat_ai` không được đóng vào ZIP source FINAL.

## Kiểm tra cuối
Xem `docs/FINAL_VALIDATION.md`.

## Việc người dùng cần làm sau khi giải nén
1. Giữ/tạo `.env` cục bộ từ `.env.example`; không copy credential ra ngoài.
2. Chạy SQL cần thiết trong `database/greenshop_required_updates.sql` bằng MySQL Workbench.
3. Chạy:
   - `composer install`
   - `npm install`
   - `php artisan optimize:clear`
   - `npm run build`
   - `php artisan route:list`
4. Smoke test các luồng: Auth, Shop, Cart, Checkout, COD, chuyển khoản, MoMo Sandbox, Orders, Reviews, AI, Account, Admin.
5. Không chạy `php artisan migrate`.
