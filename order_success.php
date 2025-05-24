<?php
$order_id = $_GET['order_id'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Đặt hàng thành công</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5 text-center">
    <h2 class="text-success">🎉 Đặt hàng thành công!</h2>
    <p>Mã đơn hàng của bạn là: <strong>#<?php echo $order_id; ?></strong></p>
    <a href="index.php" class="btn btn-primary mt-3">Tiếp tục mua hàng</a>
</div>
</body>
</html>