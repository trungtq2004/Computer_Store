<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5 col-md-6">
    <h3>Thông tin thanh toán</h3>
    <form action="orders/save-order.php" method="post">
        <div class="mb-3">
            <label>Họ tên người nhận</label>
            <input type="text" name="fullname" required class="form-control">
        </div>
        <div class="mb-3">
            <label>Số điện thoại</label>
            <input type="text" name="phone" required class="form-control">
        </div>
        <div class="mb-3">
            <label>Địa chỉ giao hàng</label>
            <textarea name="address" required class="form-control" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Xác nhận đặt hàng</button>
    </form>
</div>
</body>
</html>
