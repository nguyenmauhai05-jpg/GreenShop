    

    <div class="dashboard-stat-grid">


        
        <div class="dashboard-stat-card">

            <div class="dashboard-stat-icon">
                <svg
                    viewBox="0 0 24 24"
                    width="28"
                    height="28"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <rect x="3" y="6" width="18" height="13" rx="2"/>
                    <path d="M16 10h5v5h-5a2.5 2.5 0 0 1 0-5z"/>
                    <path d="M7 6V4h10v2"/>
                </svg>
            </div>

            <div class="dashboard-stat-content">

                <span>
                    Tổng doanh thu
                </span>

                <strong>
                    <?php echo e(number_format($tongDoanhThu ?? 0, 0, ',', '.')); ?>₫
                </strong>

                <small>
                    Doanh thu trong khoảng thời gian chọn
                </small>

            </div>

        </div>



        
        <div class="dashboard-stat-card">

            <div class="dashboard-stat-icon">
                <svg
                    viewBox="0 0 24 24"
                    width="28"
                    height="28"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <rect x="5" y="4" width="14" height="17" rx="2"/>
                    <path d="M9 4V2.8h6V4"/>
                    <path d="M8 9h8"/>
                    <path d="M8 13h8"/>
                    <path d="M8 17h5"/>
                </svg>
            </div>

            <div class="dashboard-stat-content">

                <span>
                    Tổng đơn hàng
                </span>

                <strong>
                    <?php echo e(number_format($tongDonHang ?? 0)); ?>

                </strong>

                <small>
                    Tổng số đơn hàng
                </small>

            </div>

        </div>



        
        <div class="dashboard-stat-card">

            <div class="dashboard-stat-icon">
                <svg
                    viewBox="0 0 24 24"
                    width="28"
                    height="28"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <path d="M12 21V11"/>
                    <path d="M12 11C8 11 5 8 5 4c4 0 7 3 7 7z"/>
                    <path d="M12 14c4 0 7-3 7-7-4 0-7 3-7 7z"/>
                    <path d="M8 21h8"/>
                </svg>
            </div>

            <div class="dashboard-stat-content">

                <span>
                    Tổng sản phẩm
                </span>

                <strong>
                    <?php echo e(number_format($tongSanPham ?? 0)); ?>

                </strong>

                <small>
                    Tổng số cây trong hệ thống
                </small>

            </div>

        </div>



        
        <div class="dashboard-stat-card">

            <div class="dashboard-stat-icon">
                <svg
                    viewBox="0 0 24 24"
                    width="28"
                    height="28"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <circle cx="9" cy="8" r="3"/>
                    <circle cx="17" cy="9" r="2.5"/>
                    <path d="M3.5 20v-1.5A5.5 5.5 0 0 1 9 13h0a5.5 5.5 0 0 1 5.5 5.5V20"/>
                    <path d="M15 14.2a4.6 4.6 0 0 1 5.5 4.5V20"/>
                </svg>
            </div>

            <div class="dashboard-stat-content">

                <span>
                    Tổng khách hàng
                </span>

                <strong>
                    <?php echo e(number_format($tongKhachHang ?? 0)); ?>

                </strong>

                <small>
                    Khách hàng GreenShop
                </small>

            </div>

        </div>



        
        <div class="dashboard-stat-card">

            <div class="dashboard-stat-icon">
                <svg
                    viewBox="0 0 24 24"
                    width="28"
                    height="28"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <path d="M6 3h12"/>
                    <path d="M6 21h12"/>
                    <path d="M8 3c0 4 1.5 6 4 8 2.5-2 4-4 4-8"/>
                    <path d="M8 21c0-4 1.5-6 4-8 2.5 2 4 4 4 8"/>
                </svg>
            </div>

            <div class="dashboard-stat-content">

                <span>
                    Đơn chờ xử lý
                </span>

                <strong>
                    <?php echo e(number_format($donChoXuLy ?? 0)); ?>

                </strong>

                <small>
                    Đang chờ xác nhận
                </small>

            </div>

        </div>

    </div>
<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/admin/dashboard/components/stats.blade.php ENDPATH**/ ?>