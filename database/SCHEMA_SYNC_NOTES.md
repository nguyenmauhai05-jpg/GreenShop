# GreenShop - MySQL schema sync notes (FINAL)

## Nguồn chuẩn

GreenShop chỉ dùng **MySQL**. Database production được quản lý bằng **MySQL Workbench / SQL thuần**. Laravel migration không phải nguồn schema của project này.

## Thay đổi người dùng đã chạy trên MySQL thật

Các thay đổi sau đã được xác nhận thành công trong quá trình refactor:

- `nguoi_dung.email` có UNIQUE `uq_nguoi_dung_email`.
- `don_hang.voucher_id` có FK `fk_don_hang_voucher` sang `vouchers.voucher_id`.
- `danh_gia.order_detail_id` đã đổi từ `BIGINT UNSIGNED` thành `BIGINT NULL` để khớp `chi_tiet_don_hang.order_detail_id`.
- `danh_gia.order_detail_id` có FK `fk_danh_gia_order_detail`.

`Dump(1).sql` được gửi trước các ALTER trên nên file dump cũ chưa phản ánh đầy đủ trạng thái MySQL thật. Sau khi chạy `database/greenshop_required_updates.sql`, nên export một Dump mới từ MySQL Workbench.

## Đối chiếu source FINAL với Dump

Không phát hiện bảng/cột nghiệp vụ bắt buộc mới còn thiếu:

- Các `$table` của Model đang dùng đều có trong Dump.
- Các `$fillable` của Model đang dùng đều tương ứng với cột trong Dump.
- Các `DB::table('...')` literal đều trỏ tới bảng tồn tại.
- Runtime source không còn tự tạo/sửa schema bằng `Schema::create`, `Schema::table`, `Schema::hasColumn`, `Schema::hasTable`.

## Bảng Laravel legacy

`users` và `migrations` còn trong Dump nhưng source GreenShop FINAL không tham chiếu chúng. Chúng **không được tự động xóa** vì việc DROP dữ liệu phải do người dùng xác nhận.

## Session / cache / queue

Cấu hình mặc định:

```env
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

Do đó production không cần các bảng `sessions`, `cache`, `jobs`.

## Việc còn nên chạy

Xem `database/greenshop_required_updates.sql`. Script:

1. kiểm tra và thêm UNIQUE `(cart_id, plant_id)` nếu dữ liệu giỏ hàng không trùng;
2. bổ sung các index phục vụ query thực tế nếu chúng chưa tồn tại;
3. không chạy lại các FK/UNIQUE đã được xác nhận hoàn thành.
