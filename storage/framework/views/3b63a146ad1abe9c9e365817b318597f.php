
        <section class="ai-chat-panel">


            
            <div class="ai-chat-header">

                <div class="ai-chat-user">

                    <div class="ai-chat-avatar">

                        <svg class="ai-icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 21V10"/>
                            <path d="M12 10C9 10 6 8 6 5c3 0 6 2 6 5Z"/>
                            <path d="M12 14c3 0 6-2 6-5-3 0-6 2-6 5Z"/>
                        </svg>

                    </div>


                    <div>

                        <div class="ai-chat-title-row">
                            <h2>GreenShop AI</h2>

                            <span class="ai-status-dot"></span>
                        </div>

                        <p>Trợ lý tư vấn và chăm sóc cây</p>

                    </div>

                </div>


                <?php if($cuocTroChuyen): ?>

                    <form
                        method="POST"
                        action="<?php echo e(route(
                            'cham-soc-cay.xoa',
                            $cuocTroChuyen->cuoc_tro_chuyen_id
                        )); ?>"
                        onsubmit="return confirm('Xóa cuộc trò chuyện này?');"
                    >

                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>

                        <button
                            type="submit"
                            class="delete-chat-btn"
                        >

                            <svg class="ai-icon" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M3 6h18"/>
                                <path d="M8 6V4h8v2"/>
                                <path d="M19 6l-1 14H6L5 6"/>
                                <path d="M10 11v5"/>
                                <path d="M14 11v5"/>
                            </svg>

                            <span>Xóa lịch sử</span>

                        </button>

                    </form>

                <?php endif; ?>

            </div>



            
            <div
                class="ai-chat-messages"
                id="plantChatMessages"
            >

                <?php if($cuocTroChuyen): ?>

                    <?php $__currentLoopData = $tinNhans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tinNhan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <?php if($tinNhan->nguoi_gui === 'user'): ?>

                            <div class="chat-row chat-row-user">

                                <div class="chat-bubble user-bubble">

                                    <?php if($tinNhan->anh_dinh_kem): ?>

                                        <img
                                            class="chat-uploaded-image"
                                            src="<?php echo e(asset(
                                                'storage/'
                                                .
                                                $tinNhan->anh_dinh_kem
                                            )); ?>"
                                            alt="Ảnh cây"
                                        >

                                    <?php endif; ?>


                                    <p><?php echo e(trim($tinNhan->noi_dung)); ?></p>


                                    <div class="message-meta">

                                        <?php echo e(\Carbon\Carbon::parse(
                                                $tinNhan->thoi_gian
                                            )->format('H:i')); ?>


                                    </div>

                                </div>

                            </div>


                        <?php elseif($tinNhan->nguoi_gui === 'ai'): ?>

                            <div class="chat-row chat-row-ai">

                                <div class="message-ai-avatar">

                                    <svg class="ai-icon" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M12 21V10"/>
                                        <path d="M12 10C9 10 6 8 6 5c3 0 6 2 6 5Z"/>
                                        <path d="M12 14c3 0 6-2 6-5-3 0-6 2-6 5Z"/>
                                    </svg>

                                </div>


                                <div class="chat-bubble ai-bubble">

                                    <div class="ai-reply-content" data-ai-reply><?php echo e($tinNhan->noi_dung); ?></div>

                                    <?php if(($tinNhan->recommendations ?? collect())->isNotEmpty()): ?>
                                        <script type="application/json" class="ai-history-products"><?php echo json_encode($tinNhan->recommendations, 15, 512) ?></script>
                                    <?php endif; ?>


                                    <div class="message-footer">

                                        <?php echo e(\Carbon\Carbon::parse(
                                                $tinNhan->thoi_gian
                                            )->format('H:i')); ?>


                                    </div>

                                </div>

                            </div>

                        <?php endif; ?>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php endif; ?>

            </div>



            
            <div class="ai-plant-context">

                <label for="selectedPlant">

                    <svg class="ai-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 21V10"/>
                        <path d="M12 10C9 10 6 8 6 5c3 0 6 2 6 5Z"/>
                        <path d="M12 14c3 0 6-2 6-5-3 0-6 2-6 5Z"/>
                    </svg>

                    <span>Cây cần tư vấn</span>

                </label>


                <div class="plant-select-wrap">

                    <select id="selectedPlant">

                        <option value="">
                            Không chọn cây cụ thể
                        </option>


                        <?php $__currentLoopData = $cayDaMua; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <option
                                value="<?php echo e($cay->plant_id); ?>"
                                data-name="<?php echo e($cay->ten_cay); ?>"
                                data-image="<?php echo e($cay->anh_dai_dien); ?>"
                                <?php echo e(optional($cuocTroChuyen)->plant_id
                                    ==
                                    $cay->plant_id

                                    ? 'selected'
                                    : ''); ?>

                            >

                                <?php echo e($cay->ten_cay); ?>


                                <?php if($cay->ten_danh_muc): ?>
                                    - <?php echo e($cay->ten_danh_muc); ?>

                                <?php endif; ?>

                            </option>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </select>


                    <svg
                        class="select-chevron"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path d="m7 10 5 5 5-5"/>
                    </svg>

                </div>


                <?php if($cayDaMua->isNotEmpty()): ?>

                    <span class="ai-plant-help">
                        Chỉ hiển thị cây thuộc lịch sử mua hàng của bạn.
                    </span>

                <?php endif; ?>

            </div>



            
            <div
                class="ai-image-preview"
                id="imagePreviewBox"
                hidden
            >

                <img
                    id="imagePreview"
                    src=""
                    alt="Ảnh xem trước"
                >


                <div class="image-preview-info">

                    <strong id="imageFileName">
                        Ảnh cây
                    </strong>


                    <button
                        type="button"
                        id="removeImageBtn"
                    >

                        <svg class="ai-icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M18 6 6 18"/>
                            <path d="m6 6 12 12"/>
                        </svg>

                        <span>Xóa ảnh</span>

                    </button>

                </div>

            </div>



            
            <form
                class="ai-chat-input-area"
                id="plantChatForm"
                enctype="multipart/form-data"
            >

                <?php echo csrf_field(); ?>


                <input
                    type="hidden"
                    id="conversationId"
                    value="<?php echo e(optional($cuocTroChuyen)
                            ->cuoc_tro_chuyen_id); ?>"
                >


                <input
                    type="file"
                    id="plantImageInput"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    hidden
                >


                <button
                    type="button"
                    class="attach-btn"
                    id="attachImageBtn"
                    title="Đính kèm ảnh cây"
                    aria-label="Đính kèm ảnh cây"
                >

                    <svg class="ai-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="m21.4 11.6-8.9 8.9a6 6 0 0 1-8.5-8.5l9.6-9.6a4 4 0 0 1 5.7 5.7L9.7 17.7a2 2 0 0 1-2.8-2.8l8.9-8.9"/>
                    </svg>

                </button>


                <textarea
                    id="plantChatInput"
                    maxlength="1000"
                    placeholder="Mô tả tình trạng cây của bạn..."
                    required
                ></textarea>


                <button
                    type="submit"
                    class="send-message-btn"
                    id="plantChatSubmit"
                >

                    <svg class="ai-icon send-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="m22 2-7 20-4-9-9-4Z"/>
                        <path d="M22 2 11 13"/>
                    </svg>

                    <span>Gửi</span>

                </button>

            </form>

        </section>
<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/cham_soc_cay/components/chat-panel.blade.php ENDPATH**/ ?>