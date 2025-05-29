<?php
include('../admin/check-admin.php');
include('../includes/db.php');

if (!isset($_GET['id'])) {
    header('Location: orders.php');
    exit;
}
$order_id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT orders.*, users.name AS customer_name, users.email 
                        FROM orders JOIN users ON orders.user_id = users.id WHERE orders.id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header('Location: orders.php');
    exit;
}

// Lấy chi tiết sản phẩm trong đơn
$stmt = $conn->prepare("SELECT order_details.*, products.name, products.image 
                        FROM order_details JOIN products ON order_details.product_id = products.id 
                        WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$details = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng #<?= $order_id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Chi tiết đơn hàng #<?= $order_id ?></h3>
    <p><strong>Khách hàng:</strong> <?= htmlspecialchars($order['customer_name']) ?> (<?= $order['email'] ?>)</p>
    <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address']) ?></p>
    <p><strong>Điện thoại:</strong> <?= htmlspecialchars($order['phone']) ?></p>
    <p><strong>Trạng thái:</strong> <?= $order['status'] ?></p>

    <form method="post" action="update-order-status.php">
        <input type="hidden" name="order_id" value="<?= $order_id ?>">
        <label>Thay đổi trạng thái:</label>
        <select name="status" class="form-select w-25 mb-3">
            <?php
            $statuses = ['Mới', 'Đã xác nhận', 'Đang giao hàng', 'Đã giao', 'Đã hủy'];
            foreach ($statuses as $status):
            ?>
                <option value="<?= $status ?>" <?= $order['status'] == $status ? 'selected' : '' ?>><?= $status ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Cập nhật trạng thái</button>
        <a href="orders.php" class="btn btn-secondary">Quay lại</a>
    </form>

    <h4 class="mt-4">Sản phẩm trong đơn</h4>
    <table class="table table-bordered text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>Ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Tạm tính</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $details->fetch_assoc()): ?>
            <tr>
                <td><img src="../uploads/<?= htmlspecialchars($item['image']) ?>" width="80"></td>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                <td><?= $item['quantity'] ?></td>
                <td><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ</td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
