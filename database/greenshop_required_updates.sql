-- ============================================================
-- GREENSHOP - MYSQL REQUIRED / RECOMMENDED UPDATES (FINAL)
-- Nguồn chuẩn: MySQL thực tế của người dùng + source GreenShop.
-- Chạy thủ công bằng MySQL Workbench.
-- KHÔNG dùng php artisan migrate / migrate:fresh.
-- Script này được viết theo hướng có thể chạy lại an toàn cho các index mới.
-- ============================================================

USE GreenShop;

-- ============================================================
-- A. CÁC THAY ĐỔI ĐÃ ĐƯỢC NGƯỜI DÙNG XÁC NHẬN HOÀN THÀNH
-- ============================================================
-- 1) uq_nguoi_dung_email: UNIQUE nguoi_dung(email)
-- 2) fk_don_hang_voucher: don_hang(voucher_id) -> vouchers(voucher_id)
-- 3) danh_gia.order_detail_id đã đổi thành BIGINT NULL
-- 4) fk_danh_gia_order_detail:
--    danh_gia(order_detail_id) -> chi_tiet_don_hang(order_detail_id)
--
-- KHÔNG chạy ALTER lại. Chỉ kiểm tra:
SELECT
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
  AND CONSTRAINT_NAME IN (
      'uq_nguoi_dung_email',
      'fk_don_hang_voucher',
      'fk_danh_gia_order_detail'
  )
ORDER BY TABLE_NAME, CONSTRAINT_NAME;

SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'danh_gia'
  AND COLUMN_NAME = 'order_detail_id';

-- ============================================================
-- B. DATA INTEGRITY - MỖI CÂY CHỈ CÓ 1 DÒNG TRONG MỘT GIỎ HÀNG
-- ============================================================
-- CartService cập nhật giỏ theo cặp (cart_id, plant_id), vì vậy cặp này nên
-- duy nhất. Script chỉ thêm UNIQUE nếu KHÔNG có dữ liệu trùng và constraint
-- chưa tồn tại.

SELECT cart_id, plant_id, COUNT(*) AS so_dong
FROM chi_tiet_gio_hang
GROUP BY cart_id, plant_id
HAVING COUNT(*) > 1;

SET @cart_duplicate_count := (
    SELECT COUNT(*)
    FROM (
        SELECT cart_id, plant_id
        FROM chi_tiet_gio_hang
        GROUP BY cart_id, plant_id
        HAVING COUNT(*) > 1
    ) AS duplicated_cart_items
);

SET @cart_unique_exists := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'chi_tiet_gio_hang'
      AND INDEX_NAME = 'uq_chi_tiet_gio_hang_cart_plant'
);

SET @sql := IF(
    @cart_duplicate_count = 0 AND @cart_unique_exists = 0,
    'ALTER TABLE chi_tiet_gio_hang ADD CONSTRAINT uq_chi_tiet_gio_hang_cart_plant UNIQUE (cart_id, plant_id)',
    'SELECT ''SKIP uq_chi_tiet_gio_hang_cart_plant: da ton tai hoac dang co dong gio hang trung'' AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================
-- C. INDEX TỐI ƯU THEO QUERY THỰC TẾ
-- ============================================================
-- Các index dưới đây không thay đổi nghiệp vụ. Mỗi lệnh tự kiểm tra tên index
-- trước khi ALTER để tránh lỗi Duplicate key name khi chạy lại script.

-- C1. Đơn hàng của một người dùng theo ngày
SET @exists := (SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'don_hang'
      AND INDEX_NAME = 'idx_don_hang_user_date');
SET @sql := IF(@exists = 0,
    'ALTER TABLE don_hang ADD INDEX idx_don_hang_user_date (user_id, ngay_dat)',
    'SELECT ''SKIP idx_don_hang_user_date: da ton tai'' AS message');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- C2. Dashboard / báo cáo đơn hàng theo trạng thái và ngày
SET @exists := (SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'don_hang'
      AND INDEX_NAME = 'idx_don_hang_status_date');
SET @sql := IF(@exists = 0,
    'ALTER TABLE don_hang ADD INDEX idx_don_hang_status_date (trang_thai, ngay_dat)',
    'SELECT ''SKIP idx_don_hang_status_date: da ton tai'' AS message');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- C3. Danh sách cuộc trò chuyện AI của user theo lần cập nhật mới nhất
SET @exists := (SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cuoc_tro_chuyen_ai'
      AND INDEX_NAME = 'idx_cuoc_ai_user_updated');
SET @sql := IF(@exists = 0,
    'ALTER TABLE cuoc_tro_chuyen_ai ADD INDEX idx_cuoc_ai_user_updated (user_id, thoi_gian_cap_nhat)',
    'SELECT ''SKIP idx_cuoc_ai_user_updated: da ton tai'' AS message');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- C4. Tin nhắn của một cuộc trò chuyện theo thời gian
SET @exists := (SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tin_nhan_ai'
      AND INDEX_NAME = 'idx_tin_nhan_ai_conversation_time');
SET @sql := IF(@exists = 0,
    'ALTER TABLE tin_nhan_ai ADD INDEX idx_tin_nhan_ai_conversation_time (cuoc_tro_chuyen_id, thoi_gian, tin_nhan_id)',
    'SELECT ''SKIP idx_tin_nhan_ai_conversation_time: da ton tai'' AS message');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- C5. Địa chỉ của user, ưu tiên địa chỉ mặc định
SET @exists := (SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'dia_chi'
      AND INDEX_NAME = 'idx_dia_chi_user_default');
SET @sql := IF(@exists = 0,
    'ALTER TABLE dia_chi ADD INDEX idx_dia_chi_user_default (user_id, mac_dinh, address_id)',
    'SELECT ''SKIP idx_dia_chi_user_default: da ton tai'' AS message');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- C6. Cây đang bán / cảnh báo tồn kho
SET @exists := (SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cay_canh'
      AND INDEX_NAME = 'idx_cay_canh_status_stock');
SET @sql := IF(@exists = 0,
    'ALTER TABLE cay_canh ADD INDEX idx_cay_canh_status_stock (trang_thai, so_luong)',
    'SELECT ''SKIP idx_cay_canh_status_stock: da ton tai'' AS message');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- C7. Báo cáo khách hàng mới
SET @exists := (SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'nguoi_dung'
      AND INDEX_NAME = 'idx_nguoi_dung_created_at');
SET @sql := IF(@exists = 0,
    'ALTER TABLE nguoi_dung ADD INDEX idx_nguoi_dung_created_at (created_at)',
    'SELECT ''SKIP idx_nguoi_dung_created_at: da ton tai'' AS message');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- C8. Dashboard/Admin lọc đánh giá theo số sao và ngày đánh giá
SET @exists := (SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'danh_gia'
      AND INDEX_NAME = 'idx_danh_gia_stars_date');
SET @sql := IF(@exists = 0,
    'ALTER TABLE danh_gia ADD INDEX idx_danh_gia_stars_date (so_sao, ngay_danh_gia, review_id)',
    'SELECT ''SKIP idx_danh_gia_stars_date: da ton tai'' AS message');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ============================================================
-- D. KHÔNG PHÁT HIỆN BẢNG / CỘT NGHIỆP VỤ BẮT BUỘC NÀO CÒN THIẾU
-- ============================================================
-- Source FINAL đã được đối chiếu với Dump(1).sql:
-- - Model $table: không có bảng thiếu.
-- - Model $fillable: không có cột thiếu.
-- - DB::table('...') literal: không trỏ đến bảng không tồn tại.
-- - Runtime source không dùng Schema::create/table/hasColumn/hasTable.
--
-- Session/cache/queue mặc định là file/file/sync nên KHÔNG cần tạo:
-- sessions, cache, jobs.

-- ============================================================
-- E. CHIỀU CAO CÂY - GIỮ NGUYÊN SCHEMA / NGHIỆP VỤ HIỆN TẠI
-- ============================================================
-- cay_canh.chieu_cao hiện là VARCHAR (ví dụ "30-50 cm", "152", NULL).
-- ShopCatalogService phân loại theo số đầu tiên để giữ kết quả của code cũ.
-- Chưa tách min_height/max_height vì sẽ làm thay đổi nghiệp vụ bộ lọc.

-- ============================================================
-- F. BẢNG LARAVEL LEGACY - CHỈ KIỂM TRA, KHÔNG TỰ DROP
-- ============================================================
-- Source GreenShop FINAL không dùng bảng `users` và `migrations`, nhưng dữ liệu
-- là của người dùng nên không tự xóa.
SELECT COUNT(*) AS so_dong_users FROM users;
SELECT COUNT(*) AS so_dong_migrations FROM migrations;

-- Chỉ khi tự xác nhận không cần dữ liệu mới cân nhắc:
-- DROP TABLE users;
-- DROP TABLE migrations;

-- ============================================================
-- G. MYSQL TEST DATABASE - TÙY CHỌN
-- ============================================================
-- Chỉ cần nếu muốn chạy automated tests. Không chạy test trên GreenShop thật.
-- CREATE DATABASE IF NOT EXISTS GreenShop_test
-- CHARACTER SET utf8mb4
-- COLLATE utf8mb4_unicode_ci;

-- ============================================================
-- H. ĐỒNG BỘ ĐƯỜNG DẪN ẢNH CÂY MẪU VỚI SOURCE HIỆN TẠI
-- ============================================================
-- Dump(1).sql đang tham chiếu 18 ảnh .jpg cũ nhưng source FINAL chỉ còn các
-- ảnh .png tương ứng. Các UPDATE dưới đây chỉ chạy khi đúng plant_id VÀ đúng
-- đường dẫn cũ, nên không ghi đè nếu người dùng đã thay ảnh khác trên MySQL.

UPDATE cay_canh SET anh_dai_dien = 'images/cay-canh/luoi-ho1.png'
WHERE plant_id = 61 AND anh_dai_dien = 'images/cay-canh/luoi-ho.jpg';

UPDATE cay_canh SET anh_dai_dien = 'images/cay-canh/kim-tien1.png'
WHERE plant_id = 62 AND anh_dai_dien = 'images/cay-canh/kim-tien.jpg';

UPDATE cay_canh SET anh_dai_dien = 'images/cay-canh/bang-singapore1.png'
WHERE plant_id = 63 AND anh_dai_dien = 'images/cay-canh/bang-singapore.jpg';

UPDATE cay_canh SET anh_dai_dien = 'images/cay-canh/phu-quy1.png'
WHERE plant_id = 64 AND anh_dai_dien = 'images/cay-canh/phu-quy.jpg';

UPDATE cay_canh SET anh_dai_dien = 'images/cay-canh/truc-nhat1.png'
WHERE plant_id = 65 AND anh_dai_dien = 'images/cay-canh/truc-nhat.jpg';

UPDATE cay_canh SET anh_dai_dien = 'images/cay-canh/day-nhen1.png'
WHERE plant_id = 66 AND anh_dai_dien = 'images/cay-canh/day-nhen.jpg';

UPDATE cay_canh SET anh_dai_dien = 'images/cay-canh/ngoc-ngan1.png'
WHERE plant_id = 67 AND anh_dai_dien = 'images/cay-canh/ngoc-ngan.jpg';

UPDATE cay_canh SET anh_dai_dien = 'images/cay-canh/duong-xi1.png'
WHERE plant_id = 68 AND anh_dai_dien = 'images/cay-canh/duong-xi.jpg';

UPDATE cay_canh SET anh_dai_dien = 'images/cay-canh/hong-mon1.png'
WHERE plant_id = 69 AND anh_dai_dien = 'images/cay-canh/hong-mon.jpg';

UPDATE cay_canh SET anh_dai_dien = 'images/cay-canh/trau-ba-cam-thach1.png'
WHERE plant_id = 70 AND anh_dai_dien = 'images/cay-canh/trau-ba-cam-thach.jpg';

UPDATE cay_canh SET anh_dai_dien = 'images/cay-canh/aloe-vera1.png'
WHERE plant_id = 71 AND anh_dai_dien = 'images/cay-canh/aloe-vera.jpg';

UPDATE cay_canh SET anh_dai_dien = 'images/cay-canh/xuong-rong1.png'
WHERE plant_id = 72 AND anh_dai_dien = 'images/cay-canh/xuong-rong.jpg';

UPDATE cay_canh SET anh_dai_dien = 'images/cay-canh/phat-tai1.png'
WHERE plant_id = 73 AND anh_dai_dien = 'images/cay-canh/phat-tai.jpg';

UPDATE cay_canh SET anh_dai_dien = 'images/cay-canh/sen-da-nau1.png'
WHERE plant_id = 74 AND anh_dai_dien = 'images/cay-canh/sen-da-nau.jpg';

UPDATE cay_canh SET anh_dai_dien = 'images/cay-canh/trau-ba-xanh1.png'
WHERE plant_id = 75 AND anh_dai_dien = 'images/cay-canh/trau-ba-xanh.jpg';

UPDATE cay_canh SET anh_dai_dien = 'images/cay-canh/lan-y1.png'
WHERE plant_id = 76 AND anh_dai_dien = 'images/cay-canh/lan-y.jpg';

UPDATE cay_canh SET anh_dai_dien = 'images/cay-canh/cau-tieu-tram1.png'
WHERE plant_id = 77 AND anh_dai_dien = 'images/cay-canh/cau-tieu-tram.jpg';

UPDATE cay_canh SET anh_dai_dien = 'images/cay-canh/van-nien-thanh1.png'
WHERE plant_id = 78 AND anh_dai_dien = 'images/cay-canh/van-nien-thanh.jpg';

-- Hai ảnh sau được DB tham chiếu nhưng không tồn tại trong GreenShop(4).zip,
-- vì vậy FINAL không tự gán ảnh khác để tránh sai dữ liệu sản phẩm.
SELECT plant_id, ten_cay, anh_dai_dien
FROM cay_canh
WHERE plant_id IN (79, 80);

-- Sau khi chạy script, kiểm tra nhanh 18 cây mẫu đã dùng đường dẫn có trong source:
SELECT plant_id, ten_cay, anh_dai_dien
FROM cay_canh
WHERE plant_id BETWEEN 61 AND 78
ORDER BY plant_id;

