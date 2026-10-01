{{-- =====================================================
             SIDEBAR
        ====================================================== --}}
        <aside class="ai-sidebar">

            {{-- BRAND --}}
            <div class="ai-sidebar-brand">

                <div class="ai-sidebar-avatar">
                    <svg class="ai-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 21V10"/>
                        <path d="M12 10C9 10 6 8 6 5c3 0 6 2 6 5Z"/>
                        <path d="M12 14c3 0 6-2 6-5-3 0-6 2-6 5Z"/>
                    </svg>
                </div>

                <div>
                    <h2>Trợ lý GreenShop AI</h2>
                    <p>AI tư vấn và chăm sóc cây của bạn</p>
                </div>

            </div>


            {{-- CHAT MỚI --}}
            <a
                href="{{ route('cham-soc-cay') }}"
                id="newAiConversationLink"
                class="new-chat-btn"
            >
                <svg class="ai-icon" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 5v14"/>
                    <path d="M5 12h14"/>
                </svg>

                <span>Cuộc trò chuyện mới</span>
            </a>


            {{-- =====================================================
                 LỊCH SỬ
            ====================================================== --}}
            <div class="history-heading">

                <div class="history-heading-title">

                    <svg class="ai-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 7v5l3 2"/>
                    </svg>

                    <span>LỊCH SỬ TRÒ CHUYỆN</span>

                </div>


                @if($lichSu->isNotEmpty())

                    <form
                        method="POST"
                        action="{{ route('cham-soc-cay.xoa-tat-ca') }}"
                        onsubmit="return confirm('Xóa toàn bộ lịch sử trò chuyện?');"
                    >

                        @csrf
                        @method('DELETE')

                        <button
                            class="clear-history-btn"
                            type="submit"
                            title="Xóa toàn bộ lịch sử"
                            aria-label="Xóa toàn bộ lịch sử"
                        >
                            <svg class="ai-icon" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M3 6h18"/>
                                <path d="M8 6V4h8v2"/>
                                <path d="M19 6l-1 14H6L5 6"/>
                                <path d="M10 11v5"/>
                                <path d="M14 11v5"/>
                            </svg>
                        </button>

                    </form>

                @endif

            </div>


            <div class="chat-history">

                @foreach($lichSu as $item)

                    <div class="history-item-wrapper">

                        <a
                            href="{{ route(
                                'cham-soc-cay',
                                [
                                    'conversation'
                                    =>
                                    $item->cuoc_tro_chuyen_id
                                ]
                            ) }}"
                            class="history-item
                            {{
                                optional($cuocTroChuyen)
                                    ->cuoc_tro_chuyen_id
                                ==
                                $item->cuoc_tro_chuyen_id

                                ? 'active'
                                : ''
                            }}"
                        >

                            <div class="history-icon">

                                <svg class="ai-icon" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z"/>
                                </svg>

                            </div>


                            <div class="history-content">

                                <strong>
                                    {{ $item->tieu_de }}
                                </strong>

                                <span>
                                    {{
                                        \Carbon\Carbon::parse(
                                            $item->thoi_gian_cap_nhat
                                        )->format('H:i')
                                    }}
                                </span>

                            </div>

                        </a>


                        <form
                            method="POST"
                            action="{{ route(
                                'cham-soc-cay.xoa',
                                $item->cuoc_tro_chuyen_id
                            ) }}"
                            class="history-delete-form"
                            onsubmit="return confirm('Xóa cuộc trò chuyện này?');"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="history-delete-btn"
                                title="Xóa cuộc trò chuyện"
                                aria-label="Xóa cuộc trò chuyện"
                            >

                                <svg class="ai-icon" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M18 6 6 18"/>
                                    <path d="m6 6 12 12"/>
                                </svg>

                            </button>

                        </form>

                    </div>

                @endforeach

            </div>


            {{-- =====================================================
                 TIP
            ====================================================== --}}
            <div class="ai-tip-card">

                <div class="ai-tip-icon">

                    <svg class="ai-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M9 18h6"/>
                        <path d="M10 22h4"/>
                        <path d="M8.5 14.5A7 7 0 1 1 15.5 14.5C14.5 15.3 14 16 14 18h-4c0-2-.5-2.7-1.5-3.5Z"/>
                    </svg>

                </div>

                <div>

                    <strong>Mẹo chăm cây</strong>

                    <p>
                        Chọn đúng cây đã mua, mô tả triệu chứng
                        và gửi ảnh để AI tư vấn chính xác hơn.
                    </p>

                </div>

            </div>

        </aside>
