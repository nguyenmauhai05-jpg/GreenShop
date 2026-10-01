# Hotfix Admin 2026-08-30

## Đã sửa

1. Trang **Admin > Voucher**
   - Sửa `ParseError` tại `resources/views/admin/voucher/index.blade.php`.
   - Không còn truyền mảng trực tiếp vào `@json([...])`; dữ liệu `old()` được dựng trong `@php` rồi encode an toàn sang JavaScript.
   - Đã compile Blade riêng và kiểm tra PHP của file compile: không có lỗi cú pháp.

2. Trang **Admin > Quản lý cây**
   - Xóa cột checkbox ở header.
   - Xóa checkbox ở từng dòng sản phẩm.
   - Cập nhật `colspan` trạng thái rỗng từ 10 xuống 9.
   - Xóa CSS `.checkbox-column` không còn dùng.

3. Đóng gói
   - Thêm `.gitkeep` cho các thư mục cache/runtime Laravel cần tồn tại sau khi giải nén ZIP: `storage/framework/views`, `storage/framework/sessions`, `storage/framework/cache/data`, `bootstrap/cache`.
