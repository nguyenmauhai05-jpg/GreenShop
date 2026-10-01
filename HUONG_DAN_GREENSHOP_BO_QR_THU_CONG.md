# GreenShop: Bỏ thanh toán QR thủ công (BANK_TRANSFER)

Bản vá này áp dụng cho `GreenShop(2).zip` và chỉ bao gồm các file đã sửa. Giữ nguyên COD, PayPal và **payOS (QR tự động)**.

- Không hiển thị VietQR thủ công, ngân hàng BIDV cũ hay nút “Xác nhận đã chuyển khoản”.
- Backend từ chối `BANK_TRANSFER` (kể cả gửi form thủ công); đơn COD đặt hàng bình thường, PayOS/PayPal chuyển sang cổng thanh toán và chờ xác nhận hợp lệ.
- Xóa route và thao tác Admin `xac-nhan-thanh-toan` thủ công; Admin vẫn xác nhận đơn COD, theo dõi trạng thái online và quản lý vận chuyển.
- Đơn BANK_TRANSFER cũ trong cơ sở dữ liệu vẫn hiển thị nhãn lịch sử, nhưng không thể tạo mới hoặc xác nhận thủ công bằng bản vá này. Nếu đang có đơn chuyển khoản thủ công chưa xử lý, cần đối soát và xử lý trước khi áp dụng.

## Cài đặt

1. Giải nén ZIP trong thư mục gốc project `GreenShop/`, chép các file theo đúng cấu trúc. **Không** ghi đè `.env`.
2. Trên máy phát triển, chạy `php artisan optimize:clear` rồi `npm run build` (hoặc `npm run dev`).
3. Thử COD, PayPal, PayOS. Kiểm tra PayOS tạo liên kết thành công và cập nhật qua webhook hợp lệ; thử gửi POST `BANK_TRANSFER` phải bị từ chối.
4. Thử màn hình Admin: không còn nút xác nhận chuyển khoản; không được chuyển đơn online chưa thanh toán sang vận chuyển.

## Đổi tài khoản nhận tiền payOS

- Đổi số tài khoản ngân hàng nhận tiền: đăng nhập vào `https://my.payos.vn`, kết nối tài khoản ngân hàng mới, xem/cập nhật **Kênh thanh toán** và ngân hàng liên kết với kênh. Đây là cấu hình tại payOS, không phải tài khoản `GREENSHOP_BANK_*` của VietQR thủ công.
- Nếu chọn **kênh payOS mới** hoặc tài khoản payOS khác: lấy đúng bộ `Client ID`, `API Key`, `Checksum Key` của kênh đó và cập nhật `.env` trên máy bạn:

```dotenv
PAYOS_CLIENT_ID=CLIENT_ID_KENH_MOI
PAYOS_API_KEY=API_KEY_KENH_MOI
PAYOS_CHECKSUM_KEY=CHECKSUM_KEY_KENH_MOI
```

- Mã nguồn đọc chúng từ `config/services.php` và dùng tại `app/Services/Payments/Providers/PayOSPaymentGateway.php`. Chạy `php artisan optimize:clear` sau khi thay `.env`. Kiểm tra webhook của kênh mới được trỏ đến webhook URL public của project.
- Không đưa Client ID/API Key/Checksum Key thật lên GitHub, ảnh chụp hoặc chat.

**SQL:** Không thay đổi database.
