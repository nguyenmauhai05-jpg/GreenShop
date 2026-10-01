<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>@yield('title', 'GreenShop Admin')</title>

    {{-- CSS KHUNG ADMIN --}}
    @vite(['resources/css/admin.css', 'resources/css/admin/sidebar.css'])

    {{-- CSS RIÊNG TỪNG MODULE --}}
    @yield('styles')
</head>

<body>

@include('components.system-toast')

<div class="admin-layout">

    {{-- SIDEBAR --}}
    @include('admin.layouts.sidebar')


    <div class="admin-wrapper">

        {{-- HEADER --}}
        @include('admin.layouts.header')


        {{-- MAIN CONTENT --}}
        <main class="admin-main">

            @yield('content')

        </main>

    </div>

</div>

{{-- JS RIÊNG TỪNG TRANG --}}
@yield('scripts')

</body>
</html>