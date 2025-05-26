<?php
$conn = new mysqli('localhost', 'root', '', 'computer_store');
if ($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

// Lấy danh sách đơn hàng và nối với tổng giá trị
$sql = "
    SELECT orders.id, orders.customer_name, orders.email, orders.phone, orders.address, orders.created_at, orders.status,
           SUM(products.price * order_items.quantity) AS total_amount
    FROM orders
    JOIN order_items ON orders.id = order_items.order_id
    JOIN products ON order_items.product_id = products.id
    GROUP BY orders.id, orders.customer_name, orders.email, orders.phone, orders.address, orders.created_at, orders.status
    ORDER BY orders.created_at DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <h2>📋 Danh sách đơn hàng</h2>
        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered mt-3">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Khách hàng</th>
                        <th>Email</th>
                        <th>Điện thoại</th>
                        <th>Địa chỉ</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Chi tiết</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['customer_name']) ?></td>
                            <td><?= htmlspecialchars($order['email']) ?></td>
                            <td><?= htmlspecialchars($order['phone']) ?></td>
                            <td><?= htmlspecialchars($order['address']) ?></td>
                            <td><?= $order['created_at'] ?></td>
                            <td><?= number_format($order['total_amount']) ?> VND</td>
                            <td><a href="../admin/admin_order_detail.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-info">Xem</a></td>
                            <td><?= $order['status'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">Chưa có đơn hàng nào.</div>
        <?php endif; ?>
    </div>
</body>

</html>