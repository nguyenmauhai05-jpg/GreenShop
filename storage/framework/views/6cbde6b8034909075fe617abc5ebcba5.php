<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Giới thiệu - GreenShop</title>

<?php echo app('Illuminate\Foundation\Vite')([
    'resources/css/customer/site-shell.css',
    'resources/css/gioi-thieu.css',
]); ?>

</head>
<body>



<?php echo $__env->make('trang_chu.components.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<main class="about-page">

    
    <section class="about-hero">

        <div class="about-container about-hero-grid">

            <div class="about-hero-content">

                <div class="about-label">
                    <span></span>
                    VỀ GREENSHOP
                </div>

                <h1>
                    Mang thiên nhiên
                    <br>
                    <span>đến gần bạn hơn.</span>
                </h1>

                <p>
                    GreenShop được tạo ra với mong muốn giúp mỗi không gian
                    sống trở nên xanh hơn, thư thái hơn và gần gũi với thiên
                    nhiên hơn.
                </p>

                <a
                    href="<?php echo e(route('cua-hang')); ?>"
                    class="about-primary-btn"
                >
                    Khám phá cửa hàng
                    <span>→</span>
                </a>

            </div>


            <div class="about-hero-image">

                <img
                    src="<?php echo e(asset('images/gioi-thieu/about-hero.png')); ?>"
                    alt="GreenShop - Không gian xanh"
                >

                <div class="about-hero-card">

                    <strong>GreenShop</strong>

                    <span>
                        Cây xanh cho cuộc sống an lành
                    </span>

                </div>

            </div>

        </div>

    </section>


    
    <section class="about-story">

        <div class="about-container about-story-grid">

            <div class="about-story-images">

                <div class="about-story-image large">

                    <img
                        src="<?php echo e(asset('images/gioi-thieu/story-1.png')); ?>"
                        alt="Chăm sóc cây GreenShop"
                    >

                </div>

                <div class="about-story-image small">

                    <img
                        src="<?php echo e(asset('images/gioi-thieu/story-2.png')); ?>"
                        alt="Cây cảnh GreenShop"
                    >

                </div>

            </div>


            <div class="about-story-content">

                <div class="about-label">
                    <span></span>
                    CÂU CHUYỆN CỦA CHÚNG TÔI
                </div>

                <h2>
                    Một góc xanh nhỏ,
                    <br>
                    một thay đổi lớn.
                </h2>

                <p>
                    GreenShop bắt đầu từ một niềm tin đơn giản:
                    cây xanh không chỉ là vật trang trí mà còn góp phần
                    tạo nên một không gian sống dễ chịu, cân bằng và đầy
                    sức sống.
                </p>

                <p>
                    Chúng tôi lựa chọn những cây phù hợp với nhiều loại
                    không gian khác nhau, đồng thời cung cấp thông tin
                    chăm sóc rõ ràng để ngay cả người mới bắt đầu cũng có
                    thể tự tin chăm sóc cây của mình.
                </p>

                <div class="about-signature">
                    GreenShop
                </div>

            </div>

        </div>

    </section>


     
<section class="about-values"> 

    <div class="about-container"> 

        <div class="about-section-heading"> 

            <div class="about-label centered"> 
                <span></span> 
                GIÁ TRỊ CỐT LÕI 
            </div> 

            <h2> 
                Điều GreenShop luôn hướng đến 
            </h2> 

            <p> 
                Không chỉ bán cây, chúng tôi muốn mang đến một trải 
                nghiệm chăm sóc cây đơn giản và đáng tin cậy. 
            </p> 

        </div> 


        <div class="about-values-grid"> 

            
            <div class="about-value-card"> 

                <div class="about-value-icon" aria-hidden="true">

                    <svg viewBox="0 0 24 24">
                        <path d="M12 21V10" />
                        <path d="M12 10C9 10 6 8 6 5c3 0 6 2 6 5Z" />
                        <path d="M12 14c3 0 6-2 6-5-3 0-6 2-6 5Z" />
                    </svg>

                </div> 

                <h3> 
                    Cây xanh chất lượng 
                </h3> 

                <p> 
                    Mỗi cây được lựa chọn kỹ lưỡng và cung cấp thông tin 
                    rõ ràng trước khi đến với khách hàng. 
                </p> 

            </div> 


            
            <div class="about-value-card"> 

                <div class="about-value-icon" aria-hidden="true">

                    <svg viewBox="0 0 24 24">
                        <path d="M7.5 6.5A7 7 0 0 1 19 10" />
                        <path d="M19 10V5.5" />
                        <path d="M19 10h-4.5" />
                        <path d="M16.5 17.5A7 7 0 0 1 5 14" />
                        <path d="M5 14v4.5" />
                        <path d="M5 14h4.5" />
                    </svg>

                </div> 

                <h3> 
                    Sống xanh bền vững 
                </h3> 

                <p> 
                    GreenShop khuyến khích một lối sống gần gũi hơn với 
                    thiên nhiên và có trách nhiệm với môi trường. 
                </p> 

            </div> 


            
            <div class="about-value-card"> 

                <div class="about-value-icon" aria-hidden="true">

                    <svg viewBox="0 0 24 24">
                        <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z" />
                    </svg>

                </div> 

                <h3> 
                    Chăm sóc tận tâm 
                </h3> 

                <p> 
                    Từ lựa chọn cây đến hướng dẫn chăm sóc, chúng tôi 
                    luôn mong muốn hỗ trợ khách hàng tốt nhất. 
                </p> 

            </div> 


            
            <div class="about-value-card"> 

                <div class="about-value-icon" aria-hidden="true">

                    <svg viewBox="0 0 24 24">
                        <path d="M12 3l1.7 5.3L19 10l-5.3 1.7L12 17l-1.7-5.3L5 10l5.3-1.7L12 3Z" />
                    </svg>

                </div> 

                <h3> 
                    Đơn giản và dễ tiếp cận 
                </h3> 

                <p> 
                    Dù bạn đã yêu cây từ lâu hay mới bắt đầu, 
                    GreenShop đều giúp việc chăm cây trở nên dễ dàng hơn. 
                </p> 

            </div> 

        </div> 

    </div> 

</section>


    
    <section class="about-commitment">

        <div class="about-container about-commitment-grid">

            <div class="about-commitment-content">

                <div class="about-label">
                    <span></span>
                    CAM KẾT GREENSHOP
                </div>

                <h2>
                    Đồng hành cùng bạn
                    trong từng chậu cây.
                </h2>

                <p>
                    GreenShop không dừng lại ở việc giao cây đến tay bạn.
                    Chúng tôi còn cung cấp thông tin chăm sóc, hỗ trợ tư vấn
                    và trợ lý AI để bạn có thể theo dõi và xử lý các vấn đề
                    thường gặp của cây.
                </p>


                <div class="about-check-list">

                    <div>
                        <span>✓</span>
                        Thông tin cây minh bạch
                    </div>

                    <div>
                        <span>✓</span>
                        Hướng dẫn chăm sóc dễ hiểu
                    </div>

                    <div>
                        <span>✓</span>
                        Hỗ trợ tư vấn chăm sóc cây
                    </div>

                    <div>
                        <span>✓</span>
                        Trải nghiệm mua sắm thuận tiện
                    </div>

                </div>

            </div>


            <div class="about-commitment-image">

                <img
                    src="<?php echo e(asset('images/gioi-thieu/commitment.png')); ?>"
                    alt="GreenShop chăm sóc cây"
                >

            </div>

        </div>

    </section>


    
    <section class="about-stats">

        <div class="about-container about-stats-grid">

            <div class="about-stat-item">
                <strong>100+</strong>
                <span>Loại cây xanh</span>
            </div>

            <div class="about-stat-item">
                <strong>1.000+</strong>
                <span>Khách hàng yêu cây</span>
            </div>

            <div class="about-stat-item">
                <strong>24/7</strong>
                <span>Hỗ trợ chăm sóc</span>
            </div>

            <div class="about-stat-item">
                <strong>100%</strong>
                <span>Tận tâm với từng đơn hàng</span>
            </div>

        </div>

    </section>


    
    <section class="about-cta">

        <div class="about-container">

            <div class="about-cta-box">

                <div>

                    <div class="about-label light">
                        <span></span>
                        BẮT ĐẦU KHÔNG GIAN XANH
                    </div>

                    <h2>
                        Tìm một chậu cây
                        phù hợp với bạn.
                    </h2>

                    <p>
                        Khám phá bộ sưu tập cây GreenShop và bắt đầu tạo nên
                        một không gian sống xanh hơn ngay hôm nay.
                    </p>

                </div>


                <a
                    href="<?php echo e(route('cua-hang')); ?>"
                    class="about-cta-btn"
                >
                    Xem cửa hàng
                    <span>→</span>
                </a>

            </div>

        </div>

    </section>

</main>



<?php echo $__env->make('trang_chu.components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/gioi_thieu/index.blade.php ENDPATH**/ ?>