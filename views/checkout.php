<?php
session_start();
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: ../views/cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Thông tin thanh toán</title>
</head>

<body>
    <div class="container py-5">
        <h2>Thông tin thanh toán</h2>
        <form action="../controllers/save_order.php" method="post">
            <div class="mb-3">
                <label for="customer_name" class="form-label">Họ tên</label>
                <input type="text" class="form-control" name="customer_name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" name="phone" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Địa chỉ</label>
                <textarea class="form-control" name="address" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-success">Xác nhận đặt hàng</button>
            <a href="../views/cart.php" class="btn btn-secondary">Quay lại giỏ hàng</a>
        </form>
    </div>
</body>

</html>