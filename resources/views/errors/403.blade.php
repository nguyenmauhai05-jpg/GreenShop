<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Không có quyền truy cập - GreenShop</title>
    <style>
        *{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px;background:#f7f9f6;font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;color:#243329}.card{width:min(460px,100%);background:#fff;border:1px solid #e4ebe4;border-radius:16px;padding:42px 32px;text-align:center;box-shadow:0 14px 42px rgba(30,65,38,.08)}.icon{width:72px;height:72px;border-radius:50%;background:#fff1f1;color:#c94c4c;display:grid;place-items:center;margin:0 auto 20px}.icon svg{width:34px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}h1{font-size:22px;margin:0 0 10px}p{font-size:14px;line-height:1.6;color:#78837b;margin:0 0 24px}.btn{display:inline-flex;height:42px;align-items:center;justify-content:center;padding:0 20px;border-radius:7px;background:#14752d;color:#fff;text-decoration:none;font-size:13px;font-weight:700}.btn:hover{background:#0e5f23}
    </style>
</head>
<body>
<div class="card">
    <div class="icon"><svg viewBox="0 0 24 24"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v2"/></svg></div>
    <h1>Không có quyền truy cập</h1>
    <p>{{ $exception->getMessage() ?: 'Bạn không có quyền truy cập thông tin này.' }}</p>
    <a class="btn" href="{{ route('trang-chu') }}">Quay lại trang chủ</a>
</div>
</body>
</html>
