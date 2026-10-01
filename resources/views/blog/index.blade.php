<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - GreenShop</title>
    @vite(['resources/css/customer/site-shell.css'])
    @vite('resources/css/customer/blog.css')
</head>
<body>
@include('trang_chu.components.header')

<main class="blog-page">
    <section class="blog-hero">
        <span class="blog-kicker">GREENSHOP BLOG</span>
        <h1>Góc chăm sóc cây</h1>
        <p>Mẹo đơn giản giúp bạn chọn cây, chăm cây và giữ không gian sống luôn xanh.</p>
    </section>

    <section class="blog-grid">
        <article class="blog-card">
            <span class="blog-tag">Cây trong nhà</span>
            <h2>Cách chọn vị trí phù hợp cho cây trong nhà</h2>
            <p>Ưu tiên ánh sáng phù hợp, tránh thay đổi vị trí liên tục và theo dõi tình trạng lá để điều chỉnh.</p>
            <a href="{{ route('cua-hang') }}">Xem các loại cây →</a>
        </article>

        <article class="blog-card">
            <span class="blog-tag">Chăm sóc</span>
            <h2>Tưới cây bao nhiêu là đủ?</h2>
            <p>Không phải cây nào cũng cần tưới mỗi ngày. Hãy kiểm tra độ ẩm đất và đặc điểm từng loại cây.</p>
            <a href="{{ route('cham-soc-cay') }}">Hỏi trợ lý chăm sóc →</a>
        </article>

        <article class="blog-card">
            <span class="blog-tag">Mẹo xanh</span>
            <h2>Dấu hiệu cây đang cần được chăm sóc</h2>
            <p>Lá vàng, lá rũ hoặc đất quá khô là những tín hiệu bạn nên kiểm tra điều kiện sống của cây.</p>
            <a href="{{ route('cham-soc-cay') }}">Nhận tư vấn →</a>
        </article>
    </section>
</main>
</body>
</html>
