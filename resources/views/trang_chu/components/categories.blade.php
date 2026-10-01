<section class="categories-section">

    <div class="categories-container">

        {{-- Tiêu đề --}}
        <div class="categories-heading">
            <span class="categories-label">BROWSE</span>

            <h2>Khám phá danh mục</h2>

            <p>
                Tìm cây xanh phù hợp cho mọi không gian sống của bạn.
            </p>
        </div>

        {{-- Danh sách category --}}
        <div class="categories-grid">

            {{-- CÂY TRONG NHÀ --}}
            <a href="{{ route('cua-hang', ['category' => 1]) }}" class="category-card">

                <img
                    src="{{ asset('images/trang-chu/categories/indoor.png') }}"
                    alt="Cây trong nhà"
                >

                <div class="category-overlay"></div>

                <div class="category-content">
                    <div>
                        <h3>Cây trong nhà</h3>
                        <p>Khám phá ngay</p>
                    </div>

                    <span class="category-arrow">→</span>
                </div>

            </a>


            {{-- CÂY NGOÀI TRỜI --}}
            <a href="{{ route('cua-hang', ['category' => 2]) }}" class="category-card">

                <img
                    src="{{ asset('images/trang-chu/categories/outdoor.png') }}"
                    alt="Cây ngoài trời"
                >

                <div class="category-overlay"></div>

                <div class="category-content">
                    <div>
                        <h3>Cây ngoài trời</h3>
                        <p>Khám phá ngay</p>
                    </div>

                    <span class="category-arrow">→</span>
                </div>

            </a>


            {{-- CHĂM SÓC CÂY --}}
            <a href="{{ route('cham-soc-cay') }}" class="category-card">

                <img
                    src="{{ asset('images/trang-chu/categories/plant-care.png') }}"
                    alt="Chăm sóc cây"
                >

                <div class="category-overlay"></div>

                <div class="category-content">
                    <div>
                        <h3>Chăm sóc cây</h3>
                        <p>Dịch vụ & hướng dẫn</p>
                    </div>

                    <span class="category-arrow">→</span>
                </div>

            </a>

        </div>

    </div>

</section>
