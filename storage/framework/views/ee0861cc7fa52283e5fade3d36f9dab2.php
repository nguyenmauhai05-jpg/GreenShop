

<section class="testimonials-section" id="testimonials">

    <div class="testimonials-container">

        
        <div class="testimonials-header">

            <div class="testimonials-heading">

                <span class="testimonials-eyebrow">
                    KHÁCH HÀNG NÓI GÌ
                </span>

                <h2>
                    Những chia sẻ từ<br>
                    người yêu cây
                </h2>

            </div>

            <p class="testimonials-description">
                Mỗi không gian xanh đều bắt đầu từ một câu chuyện.
                Đây là những trải nghiệm thực tế từ khách hàng GreenShop.
            </p>

        </div>


        

        <?php if(isset($danhGias) && $danhGias->count() > 0): ?>

            <div class="testimonials-grid">

                <?php $__currentLoopData = $danhGias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $danhGia): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <article class="testimonial-card">

                        
                        <div class="testimonial-quote">
                            “
                        </div>


                        
                        <div class="testimonial-stars">

                            <?php for($i = 1; $i <= 5; $i++): ?>

                                <?php if($i <= (int) $danhGia->so_sao): ?>

                                    <span class="star-active">★</span>

                                <?php else: ?>

                                    <span class="star-empty">★</span>

                                <?php endif; ?>

                            <?php endfor; ?>

                        </div>


                        
                        <p class="testimonial-content">
                            “<?php echo e($danhGia->noi_dung); ?>”
                        </p>


                        
                        <div class="testimonial-footer">

                            <div class="testimonial-avatar">

                                <?php echo e(mb_strtoupper(
                                    mb_substr(
                                        $danhGia->ho_ten ?? 'K',
                                        0,
                                        1
                                    )
                                )); ?>


                            </div>


                            <div class="testimonial-user">

                                <strong>
                                    <?php echo e($danhGia->ho_ten ?? 'Khách hàng GreenShop'); ?>

                                </strong>

                                <span>
                                    Đã mua <?php echo e($danhGia->ten_cay ?? 'sản phẩm GreenShop'); ?>

                                </span>

                            </div>

                        </div>

                    </article>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>

        <?php else: ?>

            

            <div class="testimonials-empty">

                <div class="testimonials-empty-icon">
                    ☆
                </div>

                <h3>
                    Chưa có đánh giá
                </h3>

                <p>
                    Hiện tại chưa có đánh giá nào từ khách hàng.
                </p>

            </div>

        <?php endif; ?>

    </div>

</section>
<?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/trang_chu/components/testimonials.blade.php ENDPATH**/ ?>