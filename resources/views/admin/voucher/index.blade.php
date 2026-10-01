@extends('admin.layouts.app')

@section('title', 'Quản lý voucher - GreenShop Admin')
@section('page-title', 'Quản lý voucher')

@section('styles')
    @vite('resources/css/admin/voucher.css')
@endsection

@section('content')
<div class="voucher-page">

    <div class="voucher-heading voucher-heading-actions-only">
        <button type="button" class="voucher-create-btn" onclick="openCreateVoucherModal()">
            <span>+</span> Thêm voucher mới
        </button>
    </div>

    @if(session('success'))
        <div class="voucher-alert success">✓ {{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="voucher-alert error">! {{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="voucher-alert error">
            <strong>Vui lòng kiểm tra lại dữ liệu:</strong>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <div class="voucher-stats">
        <div class="voucher-stat-card">
            <span class="voucher-stat-icon">
                <svg viewBox="0 0 24 24"><path d="M4 7h16v10H4z"/><path d="M9 7a3 3 0 0 0 6 0M9 17a3 3 0 0 1 6 0"/></svg>
            </span>
            <div><span>Tổng voucher</span><strong>{{ number_format($tongVoucher ?? 0) }}</strong></div>
        </div>
        <div class="voucher-stat-card">
            <span class="voucher-stat-icon green">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg>
            </span>
            <div><span>Đang hoạt động</span><strong>{{ number_format($dangHoatDong ?? 0) }}</strong></div>
        </div>
        <div class="voucher-stat-card">
            <span class="voucher-stat-icon orange">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
            </span>
            <div><span>Sắp diễn ra</span><strong>{{ number_format($sapDienRa ?? 0) }}</strong></div>
        </div>
        <div class="voucher-stat-card">
            <span class="voucher-stat-icon red">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="m9 9 6 6M15 9l-6 6"/></svg>
            </span>
            <div><span>Hết hạn / hết lượt</span><strong>{{ number_format($hetHan ?? 0) }}</strong></div>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.voucher.index') }}" class="voucher-filter">
        <div class="voucher-search">
            <span>⌕</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo mã hoặc tên voucher...">
        </div>

        <select name="scope">
            <option value="">Tất cả nhóm voucher</option>
            <option value="don_hang" @selected(request('scope') === 'don_hang')>Mã giảm giá</option>
            <option value="van_chuyen" @selected(request('scope') === 'van_chuyen')>Voucher phí vận chuyển</option>
        </select>

        <select name="type">
            <option value="">Tất cả loại giảm</option>
            <option value="phan_tram" @selected(request('type') === 'phan_tram')>Giảm theo %</option>
            <option value="so_tien" @selected(request('type') === 'so_tien')>Giảm số tiền</option>
        </select>

        <select name="status">
            <option value="">Tất cả trạng thái</option>
            <option value="active" @selected(request('status') === 'active')>Đang chạy</option>
            <option value="upcoming" @selected(request('status') === 'upcoming')>Sắp diễn ra</option>
            <option value="paused" @selected(request('status') === 'paused')>Tạm dừng</option>
            <option value="expired" @selected(request('status') === 'expired')>Hết hạn / hết lượt</option>
        </select>

        <button type="submit" class="voucher-filter-btn">Lọc</button>
        <a href="{{ route('admin.voucher.index') }}" class="voucher-reset-btn">↻ Xóa bộ lọc</a>
    </form>

    <section class="voucher-table-card">
        <div class="voucher-table-head">
            <div>
                <h3>Danh sách voucher</h3>
                <p>Quản lý mã, mức giảm, điều kiện và thời gian sử dụng.</p>
            </div>
        </div>

        <div class="voucher-table-scroll">
            <table class="voucher-table">
                <thead>
                    <tr>
                        <th>Mã voucher</th>
                        <th>Chương trình</th>
                        <th>Mức giảm</th>
                        <th>Nhóm</th>
                        <th>Điều kiện</th>
                        <th>Lượt dùng</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($vouchers as $voucher)
                    @php
                        $startLocal = $voucher->ngay_bat_dau?->format('Y-m-d H:i:s');
                        $endLocal = $voucher->ngay_ket_thuc?->format('Y-m-d H:i:s');
                        $currentLocal = $nowLocal ?? now('Asia/Ho_Chi_Minh')->format('Y-m-d H:i:s');

                        if (!(bool) $voucher->trang_thai) {
                            $effectiveStatus = 'Tạm dừng';
                            $effectiveClass = 'paused';
                        } elseif ((int) $voucher->da_su_dung >= (int) $voucher->so_luong) {
                            $effectiveStatus = 'Hết lượt';
                            $effectiveClass = 'expired';
                        } elseif ($endLocal && $endLocal < $currentLocal) {
                            $effectiveStatus = 'Hết hạn';
                            $effectiveClass = 'expired';
                        } elseif ($startLocal && $startLocal > $currentLocal) {
                            $effectiveStatus = 'Sắp diễn ra';
                            $effectiveClass = 'upcoming';
                        } else {
                            $effectiveStatus = 'Đang chạy';
                            $effectiveClass = 'active';
                        }
                    @endphp
                    <tr>
                        <td>
                            <div class="voucher-code-cell">
                                <span class="voucher-ticket-icon">%</span>
                                <strong>{{ $voucher->ma_voucher }}</strong>
                            </div>
                        </td>
                        <td>
                            <div class="voucher-name-cell">
                                <strong>{{ $voucher->ten_voucher }}</strong>
                                <span>{{ $voucher->mo_ta ?: 'Không có mô tả' }}</span>
                            </div>
                        </td>
                        <td>
                            @if($voucher->loai_giam === 'phan_tram')
                                <strong class="voucher-discount">{{ number_format($voucher->gia_tri_giam, 0) }}%</strong>
                                @if($voucher->giam_toi_da)
                                    <small>Tối đa {{ number_format($voucher->giam_toi_da, 0, ',', '.') }}₫</small>
                                @endif
                            @else
                                <strong class="voucher-discount">{{ number_format($voucher->gia_tri_giam, 0, ',', '.') }}₫</strong>
                            @endif
                        </td>
                        <td>
                            <span class="voucher-condition">{{ ($voucher->pham_vi ?? 'don_hang') === 'van_chuyen' ? 'Phí vận chuyển' : 'Mã giảm giá' }}</span>
                        </td>
                        <td>
                            <span class="voucher-condition">Đơn từ {{ number_format($voucher->don_hang_toi_thieu, 0, ',', '.') }}₫</span>
                        </td>
                        <td>
                            <strong>{{ number_format($voucher->da_su_dung) }}/{{ number_format($voucher->so_luong) }}</strong>
                            <div class="voucher-progress"><span style="width: {{ min(100, $voucher->so_luong > 0 ? ($voucher->da_su_dung / $voucher->so_luong) * 100 : 0) }}%"></span></div>
                        </td>
                        <td class="voucher-time">
                            <span>{{ $voucher->ngay_bat_dau->format('d/m/Y H:i') }}</span>
                            <small>→ {{ $voucher->ngay_ket_thuc->format('d/m/Y H:i') }}</small>
                        </td>
                        <td><span class="voucher-status {{ $effectiveClass }}">{{ $effectiveStatus }}</span></td>
                        <td>
                            <div class="voucher-actions">
                                <button
                                    type="button"
                                    class="voucher-action edit"
                                    title="Chỉnh sửa"
                                    onclick="openEditVoucherModal(this)"
                                    data-id="{{ $voucher->voucher_id }}"
                                    data-update-url="{{ route('admin.voucher.update', $voucher) }}"
                                    data-code="{{ $voucher->ma_voucher }}"
                                    data-name="{{ $voucher->ten_voucher }}"
                                    data-description="{{ $voucher->mo_ta }}"
                                    data-scope="{{ $voucher->pham_vi ?? 'don_hang' }}"
                                    data-type="{{ $voucher->loai_giam }}"
                                    data-value="{{ $voucher->gia_tri_giam }}"
                                    data-max="{{ $voucher->giam_toi_da }}"
                                    data-min-order="{{ $voucher->don_hang_toi_thieu }}"
                                    data-quantity="{{ $voucher->so_luong }}"
                                    data-start="{{ $voucher->ngay_bat_dau->format('Y-m-d\\TH:i') }}"
                                    data-end="{{ $voucher->ngay_ket_thuc->format('Y-m-d\\TH:i') }}"
                                    data-status="{{ $voucher->trang_thai ? 1 : 0 }}"
                                >✎</button>

                                <form method="POST" action="{{ route('admin.voucher.toggle', $voucher) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="voucher-action toggle" title="{{ $voucher->trang_thai ? 'Tạm dừng' : 'Bật lại' }}">
                                        {{ $voucher->trang_thai ? 'Ⅱ' : '▶' }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.voucher.destroy', $voucher) }}" onsubmit="return confirm('Bạn có chắc muốn xóa voucher này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="voucher-action delete" title="Xóa">♲</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="voucher-empty">Chưa có voucher nào. Hãy tạo voucher đầu tiên.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :paginator="$vouchers" />
    </section>
</div>

<div class="voucher-modal" id="voucherModal">
    <div class="voucher-modal-backdrop" onclick="closeVoucherModal()"></div>
    <div class="voucher-modal-dialog">
        <div class="voucher-modal-header">
            <div>
                <h3 id="voucherModalTitle">Thêm voucher mới</h3>
                <p>Thiết lập mã giảm giá và điều kiện áp dụng.</p>
            </div>
            <button type="button" onclick="closeVoucherModal()">×</button>
        </div>

        <form method="POST" action="{{ route('admin.voucher.store') }}" id="voucherForm">
            @csrf
            <input type="hidden" name="_method" id="voucherMethod" value="POST">

            <div class="voucher-modal-body">
                <div class="voucher-form-grid">
                    <div class="voucher-form-group">
                        <label>Mã voucher <span>*</span></label>
                        <input type="text" name="ma_voucher" id="voucherCode" maxlength="50" placeholder="VD: GREEN20" required>
                        <small>Không dùng khoảng trắng. Mã sẽ được lưu chữ in hoa.</small>
                    </div>

                    <div class="voucher-form-group">
                        <label>Tên chương trình <span>*</span></label>
                        <input type="text" name="ten_voucher" id="voucherName" maxlength="150" placeholder="VD: Giảm 20% cuối tuần" required>
                    </div>

                    <div class="voucher-form-group full">
                        <label>Mô tả</label>
                        <textarea name="mo_ta" id="voucherDescription" rows="2" maxlength="500" placeholder="Mô tả ngắn về voucher..."></textarea>
                    </div>

                    <div class="voucher-form-group">
                        <label>Nhóm voucher <span>*</span></label>
                        <select name="pham_vi" id="voucherScope" required>
                            <option value="don_hang">Mã giảm giá</option>
                            <option value="van_chuyen">Voucher phí vận chuyển</option>
                        </select>
                    </div>

                    <div class="voucher-form-group">
                        <label>Loại giảm <span>*</span></label>
                        <select name="loai_giam" id="voucherType" required onchange="syncVoucherType()">
                            <option value="phan_tram">Giảm theo phần trăm (%)</option>
                            <option value="so_tien">Giảm số tiền cố định</option>
                        </select>
                    </div>

                    <div class="voucher-form-group">
                        <label>Giá trị giảm <span>*</span></label>
                        <input type="number" min="0" step="0.01" name="gia_tri_giam" id="voucherValue" required>
                    </div>

                    <div class="voucher-form-group" id="maxDiscountGroup">
                        <label>Giảm tối đa</label>
                        <input type="number" min="0" step="1000" name="giam_toi_da" id="voucherMax" placeholder="VD: 100000">
                    </div>

                    <div class="voucher-form-group">
                        <label>Đơn hàng tối thiểu <span>*</span></label>
                        <input type="number" min="0" step="1000" name="don_hang_toi_thieu" id="voucherMinOrder" value="0" required>
                    </div>

                    <div class="voucher-form-group">
                        <label>Số lượng voucher <span>*</span></label>
                        <input type="number" min="1" name="so_luong" id="voucherQuantity" value="100" required>
                    </div>

                    <div class="voucher-form-group">
                        <label>Trạng thái <span>*</span></label>
                        <select name="trang_thai" id="voucherStatus" required>
                            <option value="1">Hoạt động</option>
                            <option value="0">Tạm dừng</option>
                        </select>
                    </div>

                    <div class="voucher-form-group">
                        <label>Bắt đầu <span>*</span></label>
                        <input type="datetime-local" name="ngay_bat_dau" id="voucherStart" required>
                    </div>

                    <div class="voucher-form-group">
                        <label>Kết thúc <span>*</span></label>
                        <input type="datetime-local" name="ngay_ket_thuc" id="voucherEnd" required>
                    </div>
                </div>
            </div>

            <div class="voucher-modal-footer">
                <button type="button" class="voucher-cancel-btn" onclick="closeVoucherModal()">Hủy</button>
                <button type="submit" class="voucher-save-btn">Lưu voucher</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
@php
    $voucherOldValues = [
        'ma_voucher' => old('ma_voucher', ''),
        'ten_voucher' => old('ten_voucher', ''),
        'mo_ta' => old('mo_ta', ''),
        'pham_vi' => old('pham_vi', 'don_hang'),
        'loai_giam' => old('loai_giam', 'phan_tram'),
        'gia_tri_giam' => old('gia_tri_giam', ''),
        'giam_toi_da' => old('giam_toi_da', ''),
        'don_hang_toi_thieu' => old('don_hang_toi_thieu', '0'),
        'so_luong' => old('so_luong', '100'),
        'trang_thai' => (string) old('trang_thai', '1'),
        'ngay_bat_dau' => old('ngay_bat_dau', ''),
        'ngay_ket_thuc' => old('ngay_ket_thuc', ''),
    ];
@endphp
<script>
window.GreenShopVoucherAdmin = {
    createUrl: {!! json_encode(route('admin.voucher.store'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!},
    hasErrors: {{ $errors->any() ? 'true' : 'false' }},
    old: {!! json_encode($voucherOldValues, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!},
};
</script>
@vite('resources/js/admin/voucher.js')
@endsection
