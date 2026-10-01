@extends('admin.layouts.app')

@section('title', 'Quản lý đánh giá - GreenShop Admin')
@section('page-title', 'Quản lý đánh giá')

@section('styles')
    @vite('resources/css/admin/danh-gia.css')
@endsection

@section('content')
@php
    $statusLabel = fn ($review) => filled($review?->phan_hoi_admin)
        ? 'Đã trả lời'
        : 'Chưa trả lời';

    $statusClass = fn ($review) => filled($review?->phan_hoi_admin)
        ? 'approved'
        : 'pending';

    $selectedImages = $selectedReview?->hinh_anh ?? [];
@endphp

<div class="admin-review-page">

    @if(session('success'))
        <div class="review-alert success">{{ session('success') }}</div>
    @endif

    @if(session('warning'))
        <div class="review-alert warning">{{ session('warning') }}</div>
    @endif

    @if(session('error'))
        <div class="review-alert error">{{ session('error') }}</div>
    @endif

    @isset($loadError)
        <div class="review-alert error">{{ $loadError }}</div>
    @endisset

    <form method="GET" action="{{ route('admin.danh-gia.index') }}" class="review-filter-panel">
        <div class="review-filter search">
            <label>Tìm kiếm đánh giá</label>
            <div class="review-input-icon">
                <span>⌕</span>
                <input
                    type="text"
                    name="q"
                    value="{{ $filters['q'] }}"
                    placeholder="Sản phẩm, người đánh giá, nội dung..."
                >
            </div>
        </div>

        <div class="review-filter">
            <label>Số sao</label>
            <select name="stars">
                <option value="all" @selected($filters['stars'] === 'all')>Tất cả</option>
                @for($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" @selected($filters['stars'] === (string)$i)>
                        {{ $i }} sao
                    </option>
                @endfor
            </select>
        </div>

        <div class="review-filter">
            <label>Trạng thái</label>
            <select name="status">
                <option value="all" @selected($filters['status'] === 'all')>Tất cả</option>
                <option value="not_replied" @selected($filters['status'] === 'not_replied')>Chưa trả lời</option>
                <option value="replied" @selected($filters['status'] === 'replied')>Đã trả lời</option>
            </select>
        </div>

        <div class="review-filter date-range">
            <label>Khoảng ngày</label>
            <div class="review-date-row">
                <input type="date" name="date_from" value="{{ $filters['date_from'] }}">
                <span>→</span>
                <input type="date" name="date_to" value="{{ $filters['date_to'] }}">
            </div>
        </div>

        <button type="submit" class="review-btn primary">Lọc</button>

        <a href="{{ route('admin.danh-gia.index') }}" class="review-btn secondary review-refresh-btn">
            ↻ Làm mới
        </a>
    </form>

    <div class="review-tabs">
        <a
            href="{{ route('admin.danh-gia.index', array_merge(request()->except(['page', 'selected']), ['status' => 'all'])) }}"
            class="{{ $filters['status'] === 'all' ? 'active' : '' }}"
        >
            Tất cả <span>{{ $counts['all'] ?? 0 }}</span>
        </a>

        <a
            href="{{ route('admin.danh-gia.index', array_merge(request()->except(['page', 'selected']), ['status' => 'not_replied'])) }}"
            class="{{ $filters['status'] === 'not_replied' ? 'active pending' : '' }}"
        >
            Chưa trả lời <span>{{ $counts['not_replied'] ?? 0 }}</span>
        </a>

        <a
            href="{{ route('admin.danh-gia.index', array_merge(request()->except(['page', 'selected']), ['status' => 'replied'])) }}"
            class="{{ $filters['status'] === 'replied' ? 'active approved' : '' }}"
        >
            Đã trả lời <span>{{ $counts['replied'] ?? 0 }}</span>
        </a>
    </div>

    <div class="review-layout">

        <section class="review-table-card">
            <div class="review-table-scroll">
                <table class="admin-review-table">
                    <thead>
                    <tr>
                        <th>STT</th>
                        <th>Sản phẩm</th>
                        <th>Người đánh giá</th>
                        <th>Số sao</th>
                        <th>Nội dung đánh giá</th>
                        <th>Trạng thái</th>
                        <th>Ngày đánh giá</th>
                        <th>Thao tác</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($reviews as $review)
                        <tr class="{{ $selectedReview && $selectedReview->review_id === $review->review_id ? 'selected-row' : '' }}">
                            <td>
                                {{ method_exists($reviews, 'firstItem') ? (($reviews->firstItem() ?? 1) + $loop->index) : $loop->iteration }}
                            </td>

                            <td>
                                <div class="review-product-cell">
                                    <div class="review-product-thumb">
                                        @if($review->cayCanh?->anh_dai_dien)
                                            <img src="{{ asset($review->cayCanh->anh_dai_dien) }}" alt="{{ $review->cayCanh->ten_cay }}">
                                        @else
                                            <span>🌱</span>
                                        @endif
                                    </div>

                                    <div>
                                        <strong>{{ $review->cayCanh?->ten_cay ?? 'Sản phẩm' }}</strong>
                                        <small>
                                            {{ $review->cayCanh?->danhMuc?->ten_danh_muc ?? 'Cây cảnh' }}
                                        </small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <strong>{{ $review->nguoiDung?->ho_ten ?? 'Khách hàng' }}</strong>
                                <small>{{ $review->nguoiDung?->email ?? '—' }}</small>
                            </td>

                            <td>
                                <div class="review-stars">
                                    @for($star = 1; $star <= 5; $star++)
                                        <span class="{{ $star <= (int)$review->so_sao ? 'filled' : '' }}">★</span>
                                    @endfor
                                </div>
                            </td>

                            <td class="review-content-cell">
                                {{ \Illuminate\Support\Str::limit($review->noi_dung ?: 'Không có nội dung', 68) }}
                            </td>

                            <td>
                                <span class="review-status {{ $statusClass($review) }}">
                                    {{ $statusLabel($review) }}
                                </span>
                            </td>

                            <td>
                                {{ $review->ngay_danh_gia?->format('d/m/Y') ?? '—' }}
                                <small>{{ $review->ngay_danh_gia?->format('H:i') ?? '' }}</small>
                            </td>

                            <td>
                                <div class="review-row-actions">
                                    <a
                                        class="review-icon-btn"
                                        title="Xem chi tiết"
                                        href="{{ route('admin.danh-gia.index', array_merge(request()->query(), ['selected' => $review->review_id])) }}"
                                    >
                                        ◉
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="review-empty-row">
                                Không có đánh giá phù hợp với bộ lọc.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$reviews" />
        </section>

        <aside class="review-detail-panel">
            @if($selectedReview)
                <div class="review-detail-head">
                    <div>
                        <small>Chi tiết đánh giá</small>
                        <h3>#DG{{ str_pad((string)$selectedReview->review_id, 4, '0', STR_PAD_LEFT) }}</h3>
                    </div>

                    <span class="review-status {{ $statusClass($selectedReview) }}">
                        {{ $statusLabel($selectedReview) }}
                    </span>
                </div>

                <section class="review-detail-section">
                    <div class="review-detail-product">
                        <div class="review-detail-product-image">
                            @if($selectedReview->cayCanh?->anh_dai_dien)
                                <img src="{{ asset($selectedReview->cayCanh->anh_dai_dien) }}" alt="{{ $selectedReview->cayCanh->ten_cay }}">
                            @else
                                <span>🌱</span>
                            @endif
                        </div>

                        <div>
                            <strong>{{ $selectedReview->cayCanh?->ten_cay ?? 'Sản phẩm' }}</strong>
                            <small>{{ $selectedReview->cayCanh?->danhMuc?->ten_danh_muc ?? 'Cây cảnh' }}</small>
                        </div>
                    </div>
                </section>

                <section class="review-detail-section">
                    <h4>Thông tin đánh giá</h4>

                    <div class="review-detail-info">
                        <span>Người đánh giá</span>
                        <strong>{{ $selectedReview->nguoiDung?->ho_ten ?? 'Khách hàng' }}</strong>
                    </div>

                    <div class="review-detail-info">
                        <span>Email</span>
                        <strong>{{ $selectedReview->nguoiDung?->email ?? '—' }}</strong>
                    </div>

                    <div class="review-detail-info">
                        <span>Mã đơn hàng</span>
                        <strong>{{ $selectedReview->chiTietDonHang?->donHang?->orderCode() ?? '—' }}</strong>
                    </div>

                    <div class="review-detail-info">
                        <span>Ngày đánh giá</span>
                        <strong>{{ $selectedReview->ngay_danh_gia?->format('d/m/Y H:i') ?? '—' }}</strong>
                    </div>

                    <div class="review-detail-info">
                        <span>Số sao</span>
                        <div class="review-stars large">
                            @for($star = 1; $star <= 5; $star++)
                                <span class="{{ $star <= (int)$selectedReview->so_sao ? 'filled' : '' }}">★</span>
                            @endfor
                        </div>
                    </div>
                </section>

                <section class="review-detail-section">
                    <h4>Nội dung đánh giá</h4>
                    <div class="review-detail-content">
                        {{ $selectedReview->noi_dung ?: 'Khách hàng không nhập nội dung.' }}
                    </div>
                </section>

                @if(!empty($selectedImages))
                    <section class="review-detail-section">
                        <h4>Hình ảnh đính kèm</h4>
                        <div class="review-detail-images">
                            @foreach($selectedImages as $image)
                                <a href="{{ asset($image) }}" target="_blank" rel="noopener">
                                    <img src="{{ asset($image) }}" alt="Ảnh đánh giá">
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if(filled($selectedReview->phan_hoi_admin))
                    <section class="review-detail-section">
                        <h4>Phản hồi của Admin</h4>
                        <div class="admin-review-reply-box">
                            <div>{{ $selectedReview->phan_hoi_admin }}</div>
                            @if($selectedReview->phan_hoi_luc)
                                <small>Trả lời lúc {{ $selectedReview->phan_hoi_luc->format('d/m/Y H:i') }}</small>
                            @endif
                        </div>
                    </section>
                @endif

                <section class="review-detail-section">
                    <h4>{{ filled($selectedReview->phan_hoi_admin) ? 'Cập nhật trả lời' : 'Trả lời đánh giá' }}</h4>

                    <form
                        method="POST"
                        action="{{ route('admin.danh-gia.reply', $selectedReview->review_id) }}"
                        class="admin-review-reply-form"
                    >
                        @csrf

                        <textarea
                            name="phan_hoi_admin"
                            rows="5"
                            maxlength="2000"
                            required
                            placeholder="Nhập nội dung trả lời khách hàng..."
                        >{{ old('phan_hoi_admin', $selectedReview->phan_hoi_admin) }}</textarea>

                        @error('phan_hoi_admin')
                            <div class="review-alert error">{{ $message }}</div>
                        @enderror

                        <button type="submit" class="review-btn primary full">
                            {{ filled($selectedReview->phan_hoi_admin) ? 'Cập nhật trả lời' : 'Gửi trả lời' }}
                        </button>
                    </form>
                </section>

                <div class="review-detail-note">
                    Đánh giá được hiển thị công khai ngay sau khi khách hàng gửi. Admin chỉ được trả lời đánh giá; không được duyệt, từ chối, xóa hoặc chỉnh sửa số sao, nội dung và hình ảnh đánh giá gốc.
                </div>

                <div class="review-detail-actions">
                    <a
                        href="{{ route('admin.danh-gia.index', request()->except('selected')) }}"
                        class="review-btn secondary full"
                    >
                        Đóng
                    </a>
                </div>
            @else
                <div class="review-empty-detail">
                    <span>☆</span>
                    <h3>Chưa chọn đánh giá</h3>
                    <p>Chọn biểu tượng “Xem” để hiển thị chi tiết đánh giá.</p>
                </div>
            @endif
        </aside>
    </div>
</div>
@endsection
