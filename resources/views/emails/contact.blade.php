<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Liên hệ mới</title>
</head>
<body>
    <h2>Thông tin liên hệ</h2>
    <p><strong>Họ tên:</strong> {{ $data['name'] }}</p>
    <p><strong>Email:</strong> {{ $data['email'] }}</p>
    <p><strong>Nội dung:</strong> {{ $data['message'] ?? 'Không có nội dung' }}</p>
</body>
</html>
