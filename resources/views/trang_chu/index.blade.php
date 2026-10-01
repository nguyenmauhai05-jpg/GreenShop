<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        GreenShop - Cây xanh cho không gian sống
    </title>

    @vite(['resources/css/customer/site-shell.css', 'resources/css/customer/home.css'])

    
</head>


<body>

    {{-- HEADER --}}
    @include('trang_chu.components.header')


    <main>

        {{-- HERO --}}
        @include('trang_chu.components.hero')

        {{-- CATEGORIES --}}
    @include('trang_chu.components.categories')

    @include('trang_chu.components.best-sellers')
    
@include('trang_chu.components.promotion')

@include('trang_chu.components.benefits')

@include('trang_chu.components.new-arrivals')


    @include('trang_chu.components.testimonials')

        {{-- Các section tiếp theo sẽ thêm dần ở đây --}}

    </main>

     @include('trang_chu.components.footer')
</body>

</html>