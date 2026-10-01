{{-- =========================================================
    TESTIMONIALS - ĐÁNH GIÁ KHÁCH HÀNG
========================================================= --}}

<section class="testimonials-section" id="testimonials">

    <div class="testimonials-container">

        {{-- =========================
            HEADER
        ========================== --}}
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


        {{-- =========================
            DANH SÁCH ĐÁNH GIÁ
        ========================== --}}

        @if(isset($danhGias) && $danhGias->count() > 0)

            <div class="testimonials-grid">

                @foreach($danhGias as $danhGia)

                    <article class="testimonial-card">

                        {{-- DẤU NGOẶC KÉP --}}
                        <div class="testimonial-quote">
                            “
                        </div>


                        {{-- SAO --}}
                        <div class="testimonial-stars">

                            @for($i = 1; $i <= 5; $i++)

                                @if($i <= (int) $danhGia->so_sao)

                                    <span class="star-active">★</span>

                                @else

                                    <span class="star-empty">★</span>

                                @endif

                            @endfor

                        </div>


                        {{-- NỘI DUNG ĐÁNH GIÁ --}}
                        <p class="testimonial-content">
                            “{{ $danhGia->noi_dung }}”
                        </p>


                        {{-- THÔNG TIN KHÁCH HÀNG --}}
                        <div class="testimonial-footer">

                            <div class="testimonial-avatar">

                                {{ mb_strtoupper(
                                    mb_substr(
                                        $danhGia->ho_ten ?? 'K',
                                        0,
                                        1
                                    )
                                ) }}

                            </div>


                            <div class="testimonial-user">

                                <strong>
                                    {{ $danhGia->ho_ten ?? 'Khách hàng GreenShop' }}
                                </strong>

                                <span>
                                    Đã mua {{ $danhGia->ten_cay ?? 'sản phẩm GreenShop' }}
                                </span>

                            </div>

                        </div>

                    </article>

                @endforeach

            </div>

        @else

            {{-- =========================
                CHƯA CÓ ĐÁNH GIÁ
            ========================== --}}

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

        @endif

    </div>

</section>
