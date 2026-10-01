<article class="checkout-card address-card">
                    <div class="card-heading-row compact-selector-heading">
                        <div><span class="step-number">1</span><h2>Địa chỉ giao hàng</h2></div>
                        <?php if($addresses->count() > 1): ?>
                            <button type="button" class="selector-toggle" id="addressListToggle" aria-expanded="false" aria-controls="checkoutAddressList">›</button>
                        <?php endif; ?>
                    </div>

                    <?php if($selectedAddress): ?>
                        <?php
                            $selectedFullAddress = collect([
                                $selectedAddress->dia_chi,
                                $selectedAddress->phuong_xa,
                                $selectedAddress->quan_huyen,
                                $selectedAddress->tinh_thanh
                            ])->filter()->join(', ');
                        ?>

                        <div class="selected-address-summary" id="selectedAddressSummary">
                            <span class="selected-address-pin">
                                <svg viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                            </span>
                            <span class="selected-address-main">
                                <strong data-address-name><?php echo e($selectedAddress->nguoi_nhan ?: Auth::user()->ho_ten); ?></strong>
                                <?php if($selectedAddress->mac_dinh): ?><em data-address-default>Mặc định</em><?php endif; ?>
                                <small data-address-phone><?php echo e($selectedAddress->so_dien_thoai ?: Auth::user()->so_dien_thoai); ?></small>
                                <span data-address-full><?php echo e($selectedFullAddress); ?></span>
                            </span>
                            <button
                                type="button"
                                class="address-edit-link"
                                id="selectedAddressEditButton"
                                data-edit-address
                                data-address-id="<?php echo e($selectedAddress->address_id); ?>"
                                data-update-url="<?php echo e(route('tai-khoan.so-dia-chi.cap-nhat', $selectedAddress)); ?>"
                                data-recipient="<?php echo e($selectedAddress->nguoi_nhan ?: Auth::user()->ho_ten); ?>"
                                data-phone="<?php echo e($selectedAddress->so_dien_thoai ?: Auth::user()->so_dien_thoai); ?>"
                                data-province="<?php echo e($selectedAddress->tinh_thanh); ?>"
                                data-ward="<?php echo e($selectedAddress->phuong_xa); ?>"
                                data-detail="<?php echo e($selectedAddress->dia_chi); ?>"
                                data-latitude="<?php echo e($selectedAddress->latitude); ?>"
                                data-longitude="<?php echo e($selectedAddress->longitude); ?>"
                                data-default="<?php echo e($selectedAddress->mac_dinh ? '1' : '0'); ?>"
                            >Sửa</button>
                        </div>

                        <div class="checkout-address-quote" id="checkoutAddressQuote" role="status" aria-live="polite">
                            <span id="shippingMapStatus"><?php echo e($shippingError ?: ($shippingQuote ? 'Đã tính phí vận chuyển theo địa chỉ này.' : 'Đang chờ tính phí vận chuyển.')); ?></span>
                            <button type="button" id="retryShippingQuote" hidden>Thử lại</button>
                        </div>
                        <?php if($addresses->count() > 1): ?>
                            <div class="checkout-address-list collapsed-selector-list" id="checkoutAddressList" hidden>
                                <?php $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $full = collect([
                                            $address->dia_chi,
                                            $address->phuong_xa,
                                            $address->quan_huyen,
                                            $address->tinh_thanh
                                        ])->filter()->join(', ');
                                        $isSelected = (int)$selectedAddress->address_id === (int)$address->address_id;
                                    ?>

                                    <div class="checkout-address-list-row <?php echo e($isSelected ? 'selected' : ''); ?>">
                                        <button
                                            type="button"
                                            class="checkout-address-option"
                                            data-address-option
                                            data-id="<?php echo e($address->address_id); ?>"
                                            data-name="<?php echo e($address->nguoi_nhan ?: Auth::user()->ho_ten); ?>"
                                            data-phone="<?php echo e($address->so_dien_thoai ?: Auth::user()->so_dien_thoai); ?>"
                                            data-full="<?php echo e($full); ?>"
                                            data-default="<?php echo e($address->mac_dinh ? '1' : '0'); ?>"
                                            data-province="<?php echo e($address->tinh_thanh); ?>"
                                            data-ward="<?php echo e($address->phuong_xa); ?>"
                                            data-detail="<?php echo e($address->dia_chi); ?>"
                                            data-latitude="<?php echo e($address->latitude); ?>"
                                            data-longitude="<?php echo e($address->longitude); ?>"
                                            data-update-url="<?php echo e(route('tai-khoan.so-dia-chi.cap-nhat', $address)); ?>"
                                        >
                                            <span class="checkout-address-radio"></span>
                                            <span class="checkout-address-content">
                                                <strong>
                                                    <?php echo e($address->nguoi_nhan ?: Auth::user()->ho_ten); ?>

                                                    <?php if($address->mac_dinh): ?><em>Mặc định</em><?php endif; ?>
                                                </strong>
                                                <small><?php echo e($address->so_dien_thoai ?: Auth::user()->so_dien_thoai); ?></small>
                                                <span><?php echo e($full); ?></span>
                                            </span>
                                        </button>
                                        <button
                                            type="button"
                                            class="address-list-edit"
                                            data-edit-address
                                            data-address-id="<?php echo e($address->address_id); ?>"
                                            data-update-url="<?php echo e(route('tai-khoan.so-dia-chi.cap-nhat', $address)); ?>"
                                            data-recipient="<?php echo e($address->nguoi_nhan ?: Auth::user()->ho_ten); ?>"
                                            data-phone="<?php echo e($address->so_dien_thoai ?: Auth::user()->so_dien_thoai); ?>"
                                            data-province="<?php echo e($address->tinh_thanh); ?>"
                                            data-ward="<?php echo e($address->phuong_xa); ?>"
                                            data-detail="<?php echo e($address->dia_chi); ?>"
                                            data-latitude="<?php echo e($address->latitude); ?>"
                                            data-longitude="<?php echo e($address->longitude); ?>"
                                            data-default="<?php echo e($address->mac_dinh ? '1' : '0'); ?>"
                                        >Sửa</button>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="missing-address">
                            <strong>Chưa có địa chỉ giao hàng.</strong>
                            <p>Vui lòng thêm địa chỉ nhận hàng trong Sổ địa chỉ trước khi đặt hàng.</p>
                            <a href="<?php echo e(url('/tai-khoan/so-dia-chi')); ?>">Mở Sổ địa chỉ</a>
                        </div>
                    <?php endif; ?>
                </article>
<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/thanh_toan/components/address.blade.php ENDPATH**/ ?>