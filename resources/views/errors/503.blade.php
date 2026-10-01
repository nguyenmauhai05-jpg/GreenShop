<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đang bảo trì - {{ $storeName }}</title>
    <style>
        *{box-sizing:border-box}body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;background:#f4f8f4;color:#213027}
        .card{width:min(520px,calc(100% - 32px));padding:38px;background:#fff;border:1px solid #dfe8e1;border-radius:16px;text-align:center;box-shadow:0 18px 45px rgba(32,90,45,.08)}
        .icon{width:66px;height:66px;border-radius:50%;margin:0 auto 18px;background:#eaf7ee;color:#168b3c;display:flex;align-items:center;justify-content:center;font-size:30px}
        h1{font-size:25px;margin:0 0 10px}p{color:#718077;line-height:1.65;margin:0}
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">🛠</div>
        <h1>{{ $storeName }} đang bảo trì</h1>
        <p>Website đang được bảo trì trong thời gian ngắn. Vui lòng quay lại sau.</p>
    </div>
</body>
</html>
