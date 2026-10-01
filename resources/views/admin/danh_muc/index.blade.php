@extends('admin.layouts.app')


@section('title', 'Quản lý danh mục - GreenShop Admin')


@section('page-title', 'Quản lý danh mục')


@section('styles')
    @vite('resources/css/admin/danh-muc.css')
@endsection


@section('content')

<div class="category-page">

    {{-- =====================================================
        HEADER
    ====================================================== --}}

    <div class="category-heading">


        <div class="category-heading-actions">

            {{-- XUẤT EXCEL --}}
            <a
                href="{{ route(
                    'admin.danh-muc.export-excel',
                    request()->only([
                        'search',
                        'status'
                    ])
                ) }}"
                class="btn-export"
            >
                ↓ Xuất Excel
            </a>


            {{-- THÊM DANH MỤC --}}
            <button
                type="button"
                class="btn-add-category"
                onclick="openCreateCategoryModal()"
            >
                + Thêm danh mục mới
            </button>

        </div>

    </div>


    {{-- =====================================================
        THÔNG BÁO
    ====================================================== --}}

    @if(session('success'))

        <div class="category-alert category-alert-success">

            <span>
                ✓
            </span>

            <div>
                {{ session('success') }}
            </div>

        </div>

    @endif


    @if(session('error'))

        <div class="category-alert category-alert-error">

            <span>
                !
            </span>

            <div>
                {{ session('error') }}
            </div>

        </div>

    @endif


    @if(!empty($loiHeThong))

        <div class="category-alert category-alert-error">

            <span>
                !
            </span>

            <div>
                Không thể tải danh sách danh mục.
                Vui lòng thử lại sau.
            </div>

        </div>

    @endif

    @include('admin.danh_muc.components.stats')
    @include('admin.danh_muc.components.filter')
    @include('admin.danh_muc.components.table')

    @include('admin.danh_muc.components.modal-create')
    @include('admin.danh_muc.components.modal-edit')
    @include('admin.danh_muc.components.modal-delete')

    <div
        id="categoryPageConfig"
        hidden
        data-base-url="{{ url('/admin/danh-muc') }}"
        data-open-create="{{ $errors->getBag('createCategory')->any() ? '1' : '0' }}"
        data-open-edit="{{ $errors->getBag('editCategory')->any() && old('category_id') ? '1' : '0' }}"
        data-edit-id="{{ (int) old('category_id', 0) }}"
        data-edit-name="{{ old('ten_danh_muc', '') }}"
        data-edit-description="{{ old('mo_ta', '') }}"
        data-edit-status="{{ old('trang_thai', 'Hiển thị') }}"
    ></div>

</div>

@endsection

@section('scripts')
    @vite('resources/js/admin/danh-muc.js')
@endsection
