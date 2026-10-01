<article class="checkout-card" id="checkoutProductsCard">
                    <div class="card-heading-row"><div><span class="step-number">2</span><h2>Sản phẩm đặt mua</h2></div><span class="item-count">{{ $cart->chiTietGioHangs->sum('so_luong') }} sản phẩm</span></div>
                    <div class="checkout-products-head"><span>Sản phẩm</span><span>Đơn giá</span><span>Số lượng</span><span>Thành tiền</span></div>
                    @foreach($cart->chiTietGioHangs as $detail)
                        @php
                            $plant = $detail->cayCanh;
                            $rawImage = $plant?->anh_dai_dien;
                            $imageUrl = $rawImage ? ((str_starts_with($rawImage, 'http://') || str_starts_with($rawImage, 'https://')) ? $rawImage : asset(ltrim($rawImage, '/'))) : null;
                            $lineTotal = (float)optional($plant)->gia * (int)$detail->so_luong;
                        @endphp
                        <div class="checkout-product-row">
                            <div class="checkout-product-info">
                                <div class="checkout-thumb">@if($imageUrl)<img src="{{ $imageUrl }}" alt="{{ $plant?->ten_cay }}">@else🌿@endif</div>
                                <div><strong>{{ $plant?->ten_cay ?? 'Sản phẩm không còn tồn tại' }}</strong><span>{{ $plant?->chieu_cao ?: 'Cây cảnh GreenShop' }}</span></div>
                            </div>
                            <div data-label="Đơn giá">{{ number_format((float)optional($plant)->gia, 0, ',', '.') }}đ</div>
                            <div data-label="Số lượng" class="checkout-qty" data-plant-id="{{ $plant?->plant_id }}" data-stock="{{ (int) ($plant?->so_luong ?? 0) }}">
                                <button type="button" class="checkout-qty-btn" data-qty-change="-1" aria-label="Giảm số lượng">−</button>
                                <span class="checkout-qty-value">{{ $detail->so_luong }}</span>
                                <button type="button" class="checkout-qty-btn" data-qty-change="1" aria-label="Tăng số lượng">+</button>
                            </div>
                            <div data-label="Thành tiền"><strong>{{ number_format($lineTotal, 0, ',', '.') }}đ</strong></div>
                        </div>
                    @endforeach
                </article>
