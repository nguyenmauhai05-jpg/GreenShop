# GD6 - Changed / Added / Deleted files

## Added

- `app/Services/Shop/ShopFilterService.php`
- `app/Services/Shop/ShopCatalogService.php`
- `resources/js/customer/cua-hang.js`
- `resources/js/customer/header.js`
- `resources/views/cua_hang/components/filter/category.blade.php`
- `resources/views/cua_hang/components/filter/price.blade.php`
- `resources/views/cua_hang/components/filter/status.blade.php`
- `resources/views/cua_hang/components/filter/size.blade.php`
- `docs/TESTING_MYSQL.md`

## Modified

- `app/Http/Controllers/CuaHangController.php`
- `resources/views/cua_hang/components/filter.blade.php`
- `resources/views/cua_hang/index.blade.php`
- `resources/views/trang_chu/components/header.blade.php`
- `vite.config.js`
- `composer.json`
- `.env.example`
- `config/database.php`
- `config/session.php`
- `config/cache.php`
- `config/queue.php`
- `phpunit.xml`
- `docs/online-payments.md`
- `database/README_MYSQL.md`
- `database/greenshop_required_updates.sql`
- `README.md`
- `docs/REFACTOR_STATUS.md`

## Deleted - đã kiểm tra reference trước khi xóa

### Empty Controllers
- `app/Http/Controllers/BaiVietController.php`
- `app/Http/Controllers/CayCanhController.php`
- `app/Http/Controllers/DanhMucController.php`
- `app/Http/Controllers/QRController.php`

### Empty Requests
- `app/Http/Requests/DanhGiaRequest.php`
- `app/Http/Requests/DatHangRequest.php`

### Empty legacy Blade files
- `resources/views/admin/bai_viet/create.blade.php`
- `resources/views/admin/bai_viet/edit.blade.php`
- `resources/views/admin/bai_viet/index.blade.php`
- `resources/views/admin/cay_canh/show.blade.php`
- `resources/views/admin/dashboard.blade.php`
- `resources/views/admin/don_hang/show.blade.php`
- `resources/views/admin/nguoi_dung/index.blade.php`
- `resources/views/admin/nguoi_dung/show.blade.php`
- `resources/views/admin/thong_ke/index.blade.php`
- `resources/views/bai_viet/chi_tiet.blade.php`
- `resources/views/bai_viet/index.blade.php`
- `resources/views/cay_canh/chi_tiet.blade.php`
- `resources/views/cay_canh/danh_muc.blade.php`
- `resources/views/cay_canh/index.blade.php`
- `resources/views/cay_canh/tim_kiem.blade.php`
- `resources/views/cua_hang/ban_do.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/footer.blade.php`
- `resources/views/layouts/header.blade.php`
- `resources/views/layouts/sidebar.blade.php`
- `resources/views/lien_he/index.blade.php`
- `resources/views/qr/chat.blade.php`
- `resources/views/tai_khoan/cay_da_mua.blade.php`
- `resources/views/tai_khoan/doi_mat_khau.blade.php`
- `resources/views/thanh_toan/thanh_cong.blade.php`
- `resources/views/thanh_toan/that_bai.blade.php`
- `resources/views/thong_bao/index.blade.php`

### Laravel default legacy
- `resources/views/welcome.blade.php`
- `database/.gitignore` (chỉ chứa rule SQLite cũ)

## Không đổi nghiệp vụ

- Search + filter Cửa hàng giữ nguyên quy tắc cũ.
- Nếu filter lỗi, search vẫn chạy nhưng category/price/status/size không được áp dụng.
- `best-selling` vẫn giữ hành vi cũ (sort theo `plant_id`) vì chưa thay đổi nghiệp vụ.
- Chiều cao dạng chuỗi vẫn được phân loại theo giá trị số đầu tiên để tránh thay đổi kết quả hiện tại.
