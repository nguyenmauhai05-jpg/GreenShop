<article class="checkout-card address-card">
                    <div class="card-heading-row compact-selector-heading">
                        <div><span class="step-number">1</span><h2>Địa chỉ giao hàng</h2></div>
                        @if($addresses->count() > 1)
                            <button type="button" class="selector-toggle" id="addressListToggle" aria-expanded="false" aria-controls="checkoutAddressList">›</button>
                        @endif
                    </div>

                    @if($selectedAddress)
                        @php
                            $selectedFullAddress = collect([
                                $selectedAddress->dia_chi,
                                $selectedAddress->phuong_xa,
                                $selectedAddress->quan_huyen,
                                $selectedAddress->tinh_thanh
                            ])->filter()->join(', ');
                        @endphp

                        <div class="selected-address-summary" id="selectedAddressSummary">
                            <span class="selected-address-pin">
                                <svg viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                            </span>
                            <span class="selected-address-main">
                                <strong data-address-name>{{ $selectedAddress->nguoi_nhan ?: Auth::user()->ho_ten }}</strong>
                                @if($selectedAddress->mac_dinh)<em data-address-default>Mặc định</em>@endif
                                <small data-address-phone>{{ $selectedAddress->so_dien_thoai ?: Auth::user()->so_dien_thoai }}</small>
                                <span data-address-full>{{ $selectedFullAddress }}</span>
                            </span>
                            <button
                                type="button"
                                class="address-edit-link"
                                id="selectedAddressEditButton"
                                data-edit-address
                                data-address-id="{{ $selectedAddress->address_id }}"
                                data-update-url="{{ route('tai-khoan.so-dia-chi.cap-nhat', $selectedAddress) }}"
                                data-recipient="{{ $selectedAddress->nguoi_nhan ?: Auth::user()->ho_ten }}"
                                data-phone="{{ $selectedAddress->so_dien_thoai ?: Auth::user()->so_dien_thoai }}"
                                data-province="{{ $selectedAddress->tinh_thanh }}"
                                data-ward="{{ $selectedAddress->phuong_xa }}"
                                data-detail="{{ $selectedAddress->dia_chi }}"
                                data-latitude="{{ $selectedAddress->latitude }}"
                                data-longitude="{{ $selectedAddress->longitude }}"
                                data-default="{{ $selectedAddress->mac_dinh ? '1' : '0' }}"
                            >Sửa</button>
                        </div>

                        <div class="checkout-address-quote" id="checkoutAddressQuote" role="status" aria-live="polite">
                            <span id="shippingMapStatus">{{ $shippingError ?: ($shippingQuote ? 'Đã tính phí vận chuyển theo địa chỉ này.' : 'Đang chờ tính phí vận chuyển.') }}</span>
                            <button type="button" id="retryShippingQuote" hidden>Thử lại</button>
                        </div>
                        @if($addresses->count() > 1)
                            <div class="checkout-address-list collapsed-selector-list" id="checkoutAddressList" hidden>
                                @foreach($addresses as $address)
                                    @php
                                        $full = collect([
                                            $address->dia_chi,
                                            $address->phuong_xa,
                                            $address->quan_huyen,
                                            $address->tinh_thanh
                                        ])->filter()->join(', ');
                                        $isSelected = (int)$selectedAddress->address_id === (int)$address->address_id;
                                    @endphp

                                    <div class="checkout-address-list-row {{ $isSelected ? 'selected' : '' }}">
                                        <button
                                            type="button"
                                            class="checkout-address-option"
                                            data-address-option
                                            data-id="{{ $address->address_id }}"
                                            data-name="{{ $address->nguoi_nhan ?: Auth::user()->ho_ten }}"
                                            data-phone="{{ $address->so_dien_thoai ?: Auth::user()->so_dien_thoai }}"
                                            data-full="{{ $full }}"
                                            data-default="{{ $address->mac_dinh ? '1' : '0' }}"
                                            data-province="{{ $address->tinh_thanh }}"
                                            data-ward="{{ $address->phuong_xa }}"
                                            data-detail="{{ $address->dia_chi }}"
                                            data-latitude="{{ $address->latitude }}"
                                            data-longitude="{{ $address->longitude }}"
                                            data-update-url="{{ route('tai-khoan.so-dia-chi.cap-nhat', $address) }}"
                                        >
                                            <span class="checkout-address-radio"></span>
                                            <span class="checkout-address-content">
                                                <strong>
                                                    {{ $address->nguoi_nhan ?: Auth::user()->ho_ten }}
                                                    @if($address->mac_dinh)<em>Mặc định</em>@endif
                                                </strong>
                                                <small>{{ $address->so_dien_thoai ?: Auth::user()->so_dien_thoai }}</small>
                                                <span>{{ $full }}</span>
                                            </span>
                                        </button>
                                        <button
                                            type="button"
                                            class="address-list-edit"
                                            data-edit-address
                                            data-address-id="{{ $address->address_id }}"
                                            data-update-url="{{ route('tai-khoan.so-dia-chi.cap-nhat', $address) }}"
                                            data-recipient="{{ $address->nguoi_nhan ?: Auth::user()->ho_ten }}"
                                            data-phone="{{ $address->so_dien_thoai ?: Auth::user()->so_dien_thoai }}"
                                            data-province="{{ $address->tinh_thanh }}"
                                            data-ward="{{ $address->phuong_xa }}"
                                            data-detail="{{ $address->dia_chi }}"
                                            data-latitude="{{ $address->latitude }}"
                                            data-longitude="{{ $address->longitude }}"
                                            data-default="{{ $address->mac_dinh ? '1' : '0' }}"
                                        >Sửa</button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                    @else
                        <div class="missing-address">
                            <strong>Chưa có địa chỉ giao hàng.</strong>
                            <p>Vui lòng thêm địa chỉ nhận hàng trong Sổ địa chỉ trước khi đặt hàng.</p>
                            <a href="{{ url('/tai-khoan/so-dia-chi') }}">Mở Sổ địa chỉ</a>
                        </div>
                    @endif
                </article>
