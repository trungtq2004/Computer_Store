<?php
session_start();
include('../includes/db.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$fullname = $_POST['fullname'];
$phone = $_POST['phone'];
$address = $_POST['address'];

// Lấy sản phẩm từ bảng cart_items
$sql = "SELECT c.product_id, c.quantity, p.price
        FROM cart_items c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result();

if ($cart_items->num_rows == 0) {
    echo "<div class='alert alert-warning'>Giỏ hàng trống!</div>";
    exit;
}

// Tính tổng
$total = 0;
$items = [];
while ($row = $cart_items->fetch_assoc()) {
    $items[] = $row;
    $total += $row['price'] * $row['quantity'];
}

// Tạo đơn hàng
$insert_order = $conn->prepare("INSERT INTO orders (user_id, fullname, phone, address, total, status) VALUES (?, ?, ?, ?, ?, 'Mới')");
$insert_order->bind_param("isssi", $user_id, $fullname, $phone, $address, $total);
$insert_order->execute();
$order_id = $insert_order->insert_id;

// Thêm chi tiết đơn hàng
$insert_detail = $conn->prepare("INSERT INTO order_details (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)");
foreach ($items as $item) {
    $insert_detail->bind_param("iidi", $order_id, $item['product_id'], $item['price'], $item['quantity']);
    $insert_detail->execute();
}

// Xoá giỏ hàng trong CSDL
$delete_cart = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
$delete_cart->bind_param("i", $user_id);
$delete_cart->execute();

// Giao diện thành công
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt hàng thành công</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="alert alert-success">
        🎉 Cảm ơn bạn đã đặt hàng! Mã đơn hàng của bạn là <strong>#<?= $order_id ?></strong>.
        <br><a href="../index.php" class="btn btn-primary mt-3">Tiếp tục mua hàng</a>
    </div>
</div>
</body>
</html>
