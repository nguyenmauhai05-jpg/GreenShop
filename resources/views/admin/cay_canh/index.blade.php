@extends('admin.layouts.app')


@section('title', 'Quản lý cây - GreenShop Admin')


@section('page-title', 'Quản lý cây')


@section('styles')
    @vite('resources/css/admin/cay-canh.css')
@endsection


@section('content')

<div class="plant-management">


    {{-- =====================================================
        HEADER
    ====================================================== --}}

    <div class="plant-page-heading">


        <div class="plant-heading-actions">

            <a
    href="{{ route(
        'admin.cay-canh.export-excel',
        request()->only([
            'search',
            'category',
            'status',
            'stock'
        ])
    ) }}"
    class="plant-export-btn"
>
    ↓ Xuất Excel
</a>


            <a
                href="{{ route('admin.cay-canh.create') }}"
                class="plant-create-btn"
            >
                + Thêm cây mới
            </a>

        </div>

    </div>


    {{-- =====================================================
        THÔNG BÁO
    ====================================================== --}}

    @if(session('success'))

        <div class="plant-alert plant-alert-success" id="plantSuccessAlert">
            ✓ {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div class="plant-alert plant-alert-error">
            {{ session('error') }}
        </div>

    @endif


    @if(!empty($loiHeThong))

        <div class="plant-alert plant-alert-error">
            Không thể tải danh sách cây.
            Vui lòng thử lại sau.
        </div>

    @endif

    @include('admin.cay_canh.components.stats')
    @include('admin.cay_canh.components.filter')
    @include('admin.cay_canh.components.table')

</div>

@include('admin.cay_canh.components.delete-modal')

<div id="plantPageConfig" hidden data-base-url="{{ url('/admin/cay-canh') }}"></div>

@endsection

@section('scripts')
    @vite('resources/js/admin/cay-canh.js')
@endsection
