@props(['paginator'])

@if(is_object($paginator) && method_exists($paginator, 'total') && $paginator->total() > 0)
    @php
        $current = $paginator->currentPage();
        $last = max(1, $paginator->lastPage());
        $pages = [];

        for ($page = 1; $page <= $last; $page++) {
            if ($page === 1 || $page === $last || abs($page - $current) <= 1) {
                $pages[] = $page;
            }
        }
    @endphp

    <nav class="gs-pagination" aria-label="Phân trang">
        @if($paginator->onFirstPage())
            <span class="gs-page-btn is-disabled" aria-disabled="true" aria-label="Trang trước">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            </span>
        @else
            <a class="gs-page-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Trang trước">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            </a>
        @endif

        @php $previousRendered = null; @endphp
        @foreach($pages as $page)
            @if($previousRendered !== null && $page - $previousRendered > 1)
                <span class="gs-page-dots" aria-hidden="true">…</span>
            @endif

            @if($page === $current)
                <span class="gs-page-btn is-active" aria-current="page">{{ $page }}</span>
            @else
                <a class="gs-page-btn" href="{{ $paginator->url($page) }}" aria-label="Trang {{ $page }}">{{ $page }}</a>
            @endif

            @php $previousRendered = $page; @endphp
        @endforeach

        @if($paginator->hasMorePages())
            <a class="gs-page-btn" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Trang sau">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
            </a>
        @else
            <span class="gs-page-btn is-disabled" aria-disabled="true" aria-label="Trang sau">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
            </span>
        @endif
    </nav>
@endif
