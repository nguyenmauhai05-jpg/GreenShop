-- ============================================================
-- GREENSHOP - OPTIONAL LEGACY CLEANUP
-- ============================================================
-- KHÔNG bắt buộc để website chạy.
-- Chỉ dùng sau khi đã backup/export DB và xác nhận các bảng dưới đây
-- không còn dữ liệu cần giữ.
-- Source FINAL không reference trực tiếp các bảng: bai_viet, hinh_anh_cay,
-- lien_he, users, migrations.
--
-- LƯU Ý:
-- - KHÔNG xóa password_reset_tokens: Laravel Password Broker đang dùng.
-- - KHÔNG xóa ma_qr: PlantManagementService đang query trực tiếp.
-- ============================================================

SELECT 'bai_viet' AS bang, COUNT(*) AS so_dong FROM bai_viet
UNION ALL SELECT 'hinh_anh_cay', COUNT(*) FROM hinh_anh_cay
UNION ALL SELECT 'lien_he', COUNT(*) FROM lien_he
UNION ALL SELECT 'users', COUNT(*) FROM users
UNION ALL SELECT 'migrations', COUNT(*) FROM migrations;

-- Trong Dump(1).sql đã đối chiếu:
-- bai_viet      : không có INSERT mẫu
-- hinh_anh_cay  : không có INSERT mẫu
-- lien_he       : không có INSERT mẫu
-- users         : không có INSERT mẫu
-- migrations    : có 2 dòng lịch sử migration cũ
--
-- Chỉ bỏ comment và chạy DROP khi BẠN xác nhận dữ liệu không cần giữ.
-- DROP TABLE IF EXISTS bai_viet;
-- DROP TABLE IF EXISTS hinh_anh_cay;
-- DROP TABLE IF EXISTS lien_he;
-- DROP TABLE IF EXISTS users;
-- DROP TABLE IF EXISTS migrations;
