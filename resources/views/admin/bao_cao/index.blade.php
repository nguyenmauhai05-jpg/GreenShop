@extends('admin.layouts.app')


@section('title', 'Báo cáo thống kê - GreenShop Admin')


@section('page-title', 'Báo cáo thống kê')


@section('styles')
    @vite('resources/css/admin/bao-cao.css')
@endsection


@section('content')

<div class="report-page">

    {{-- HEADER --}}
    @include('admin.bao_cao.components.heading')


    {{-- THÔNG BÁO --}}
    @if(session('success'))

        <div class="report-alert report-alert-success">
            {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div class="report-alert report-alert-error">
            {{ session('error') }}
        </div>

    @endif


    @if(!empty($loiHeThong))

        <div class="report-alert report-alert-error">
            Không thể tải dữ liệu báo cáo.
            Vui lòng thử lại sau.
        </div>

    @endif


    {{-- FILTER --}}
    @include('admin.bao_cao.components.filters')


    @if(!empty($khongCoDuLieu))

        @include('admin.bao_cao.components.empty-state')

    @else

        {{-- SUMMARY --}}
        @include('admin.bao_cao.components.summary-cards')


        {{-- BIỂU ĐỒ + DANH MỤC --}}
        <div class="report-grid report-grid-top">

            @include('admin.bao_cao.components.revenue-chart')

            @include('admin.bao_cao.components.category-revenue')

        </div>


        {{-- BẢNG SP + TRẠNG THÁI ĐƠN --}}
        <div class="report-grid report-grid-bottom">

            @include('admin.bao_cao.components.product-report')

            @include('admin.bao_cao.components.order-status')

        </div>

    @endif

</div>

@endsection


@section('scripts')

<script>
    window.reportChartData = @json($duLieuBieuDo ?? []);
    window.reportCategoryData = @json($doanhThuDanhMuc ?? []);
    window.reportGroupBy = @json($nhomTheo ?? 'ngay');
</script>

@vite('resources/js/admin/bao-cao.js')

@endsection