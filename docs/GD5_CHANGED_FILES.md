# GreenShop - Giai đoạn 5

## Mục tiêu

- Giảm các Blade quá lớn bằng partial/component đúng theo module thực tế.
- Đưa JavaScript của các trang lớn ra `resources/js` để Vite quản lý.
- Không đổi route/API/nghiệp vụ đang chạy.
- Rà tiếp MySQL theo truy vấn thực tế và đề xuất index/data-integrity SQL.

## Blade đã tách

### `resources/views/admin/danh_muc/index.blade.php`
- Trước: ~1306 dòng.
- Sau: ~138 dòng.
- Tách:
  - `admin/danh_muc/components/stats.blade.php`
  - `admin/danh_muc/components/filter.blade.php`
  - `admin/danh_muc/components/table.blade.php`
- Modal create/edit/delete cũ được giữ nguyên.
- JavaScript chuyển sang `resources/js/admin/danh-muc.js`.

### `resources/views/admin/cay_canh/index.blade.php`
- Trước: ~1128 dòng.
- Sau: ~102 dòng.
- Tách:
  - `admin/cay_canh/components/stats.blade.php`
  - `admin/cay_canh/components/filter.blade.php`
  - `admin/cay_canh/components/table.blade.php`
  - `admin/cay_canh/components/delete-modal.blade.php`
- JavaScript chuyển sang `resources/js/admin/cay-canh.js`.

### `resources/views/cham_soc_cay/index.blade.php`
- Trước: ~1184 dòng.
- Sau: ~22 dòng.
- Tách:
  - `cham_soc_cay/components/sidebar.blade.php`
  - `cham_soc_cay/components/chat-panel.blade.php`
- JavaScript chat/image upload chuyển sang `resources/js/customer/cham-soc-cay.js`.
- Route AJAX được truyền qua `data-chat-url` / `data-base-url`, không hard-code URL trong JS.

## Vite

Đã thêm input:

- `resources/js/admin/danh-muc.js`
- `resources/js/admin/cay-canh.js`
- `resources/js/customer/cham-soc-cay.js`

## Database audit GD5

Không phát hiện bảng/cột mới mà source đang truy cập nhưng `Dump(1).sql` không có.

Đề xuất bổ sung index theo query thực tế và UNIQUE cho `(cart_id, plant_id)` trong `chi_tiet_gio_hang`. Xem:

`database/greenshop_required_updates.sql`

## Vấn đề DB cần chốt sau

`cay_canh.chieu_cao` là `VARCHAR`, trong khi trang cửa hàng đang so sánh bằng toán tử số. Dữ liệu hiện có dạng `30-50 cm`, `152`, `NULL`. Chưa thay đổi trong GD5 vì cần chốt cách hiểu kích thước cây trước khi sửa schema/nghiệp vụ.
