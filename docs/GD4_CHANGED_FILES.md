# GD4 - Changed Files

## Mục tiêu
Tiếp tục Giai đoạn 3: tách các Controller lớn thành Service theo trách nhiệm, không đổi nghiệp vụ và không thay đổi schema bằng Laravel.

## Tạo mới - Quản lý cây
- `app/Services/Plants/PlantQueryService.php`
- `app/Services/Plants/PlantImageService.php`
- `app/Services/Plants/PlantManagementService.php`
- `app/Services/Plants/PlantExcelExportService.php`

## Sửa - Quản lý cây
- `app/Http/Controllers/Admin/CayCanhController.php`
  - từ khoảng 1258 dòng xuống khoảng 179 dòng
  - Controller chỉ orchestration/response/log
  - query/filter/thống kê -> PlantQueryService
  - upload/xóa ảnh -> PlantImageService
  - CRUD + kiểm tra dữ liệu liên quan -> PlantManagementService
  - tạo Excel -> PlantExcelExportService

## Tạo mới - Báo cáo
- `app/Services/Reports/ReportFilterService.php`
- `app/Services/Reports/ReportDataService.php`
- `app/Services/Reports/ReportExportService.php`

## Sửa - Báo cáo
- `app/Http/Controllers/Admin/BaoCaoController.php`
  - từ khoảng 1945 dòng xuống khoảng 107 dòng
  - parse/validate bộ lọc -> ReportFilterService
  - query/thống kê/biểu đồ/sản phẩm -> ReportDataService
  - Excel/PDF -> ReportExportService

## Tạo mới - AI chăm sóc cây
- `app/Services/AI/PlantContextService.php`
- `app/Services/AI/AIConversationService.php`
- `app/Services/AI/AIImageService.php`
- `app/Services/AI/GeminiCareService.php`

## Sửa - AI
- `app/Http/Controllers/AIController.php`
  - từ khoảng 1282 dòng xuống khoảng 193 dòng
  - lịch sử/cuộc chat/tin nhắn -> AIConversationService
  - cây đã mua/context -> PlantContextService
  - ảnh chat -> AIImageService
  - gọi Gemini -> GeminiCareService
  - sửa lỗi gõ lặp `$message = $message = ...`
- `routes/web.php`
  - thêm `whereNumber('id')` cho route xóa cuộc trò chuyện AI.

## Database
- `database/greenshop_required_updates.sql`
  - cập nhật trạng thái 3 constraint đã được người dùng chạy thành công.
  - GD4 không yêu cầu bảng/cột mới.

## Không đổi
- Payment/MoMo services.
- Tên bảng/cột/API/route name nghiệp vụ hiện tại.
- Logic lọc cây, giá trị trạng thái, logic xóa/ẩn cây.
- Prompt và cấu hình Gemini.
- Cấu trúc dữ liệu báo cáo/Excel/PDF.

## Chuẩn hóa Admin route / schema cố định
- Tạo `routes/admin/danh-gia.php`
  - route đánh giá Admin chuyển sang `auth + admin` giống các module Admin khác.
  - thêm `whereNumber('danhGia')`.
- Sửa `routes/web.php`
  - bỏ 2 route Admin đánh giá nằm lẫn trong group khách hàng `auth`.
- Sửa `app/Http/Controllers/Admin/DanhGiaController.php`
  - bỏ `Schema::hasColumn()` vì `Dump(1).sql` đã xác nhận có `phan_hoi_admin`, `phan_hoi_luc`.
  - bỏ kiểm tra quyền Admin trùng lặp trong Controller; quyền nằm tại middleware route.
- Sửa `app/Http/Controllers/Admin/DanhMucController.php`
  - bỏ toàn bộ `Schema::hasColumn()` legacy vì DB chuẩn đã có `trang_thai`, `created_at`, `updated_at`.
  - bỏ kiểm tra Admin trùng lặp.
  - error fallback dùng paginator rỗng thay vì Collection để view không lỗi khi gọi `total()/links()`.
- Sửa `app/Http/Controllers/Admin/DonHangController.php`
- Sửa `app/Http/Controllers/Admin/VoucherController.php`
- Sửa `app/Http/Controllers/Admin/CaiDatHeThongController.php`
  - bỏ kiểm tra role trùng lặp; các route tương ứng đã có `['auth', 'admin']`.

Sau GD4, việc xác thực quyền Admin tập trung tại `AdminMiddleware` thay vì mỗi Controller tự query/đọc role theo một kiểu khác nhau.
