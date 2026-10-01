<aside class="shop-sidebar">
    <form
        method="GET"
        action="{{ route('cua-hang') }}"
        class="shop-filter-form"
        id="shopFilterForm"
    >
        <div class="filter-heading">BỘ LỌC</div>

        {{-- Giữ search để Search + Filter hoạt động đồng thời. --}}
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif

        @if(!empty($filterErrors))
            <div class="filter-error-box">
                <strong>Bộ lọc chưa hợp lệ</strong>
                @foreach($filterErrors as $message)
                    <div>{{ $message }}</div>
                @endforeach
            </div>
        @endif

        @include('cua_hang.components.filter.category')
        @include('cua_hang.components.filter.price')
        @include('cua_hang.components.filter.size')

        {{-- Giữ sort nhưng không giữ page: áp dụng filter mới sẽ quay về trang 1. --}}
        @if(request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif

        <button type="submit" class="filter-submit-btn">
            Áp dụng bộ lọc
        </button>

        <a
            href="{{ route('cua-hang', request('search') ? ['search' => request('search')] : []) }}"
            class="filter-reset-btn"
        >
            ↻ Xóa bộ lọc
        </a>
    </form>
</aside>
