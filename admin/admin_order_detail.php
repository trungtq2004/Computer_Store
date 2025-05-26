<?php
$conn = new mysqli('localhost', 'root', '', 'computer_store');
if ($conn->connect_error) die("Lỗi kết nối DB: " . $conn->connect_error);

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy thông tin khách hàng
$order = $conn->query("SELECT * FROM orders WHERE id = $order_id")->fetch_assoc();

// Lấy sản phẩm đã mua
$sql = "
    SELECT products.name, products.price, products.image, order_items.quantity
    FROM order_items
    JOIN products ON order_items.product_id = products.id
    WHERE order_items.order_id = $order_id
";

$items = $conn->query($sql);
?>
<?php if ($order['status'] == 'Mới'): ?>
    <form method="post" action="../controllers/update_order_status.php" class="mt-2">
        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
        <input type="hidden" name="status" value="Đã xác nhận">
        <button type="submit" class="btn btn-success">✅ Xác nhận đơn hàng</button>
    </form>
<?php endif; ?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
        Cập nhật trạng thái đơn hàng thành công!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <h3>📦 Chi tiết đơn hàng #<?= $order_id ?></h3>
        <br>
        <p><strong>Khách hàng: </strong> <?= htmlspecialchars($order['customer_name']) ?></p>
        <p><strong>Email: </strong> <?= htmlspecialchars($order['email']) ?></p>
        <p><strong>Điện thoại: </strong> <?= htmlspecialchars($order['phone']) ?></p>
        <p><strong>Địa chỉ: </strong> <?= htmlspecialchars($order['address']) ?></p>
        <hr>

        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0;
                while ($row = $items->fetch_assoc()): ?>
                    <?php
                    $subtotal = $row['price'] * $row['quantity'];
                    $total += $subtotal;
                    $image = "../assets/images/" . $row['image'];
                    ?>
                    <tr>
                        <td><img src="<?= $image ?>" width="80" height="80" class="rounded"></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= number_format($row['price']) ?> VND</td>
                        <td><?= $row['quantity'] ?></td>
                        <td><?= number_format($subtotal) ?> VND</td>
                    </tr>
                <?php endwhile; ?>
                <tr class="fw-bold table-light">
                    <td colspan="4" class="text-end">Tổng cộng</td>
                    <td><?= number_format($total) ?> VND</td>
                </tr>
                <tr>

                    <?php $statusColor = [
                        'Mới' => 'Secondary',
                        'Đã xác nhận' => 'primary',
                        'Đang giao hàng' => 'warning',
                        'Đã giao' => 'success',
                        'Đã hủy' => 'danger'
                    ];
                    $badgeColor = $statusColor[$order['status']] ?? 'Secondary';
                    ?>
                    <p><strong>Trạng thái: </strong> <span class="badge bg -<?= $badgeColor ?>"> <?= $order['status'] ?> </span> </p>
                    <td>
                        <form method="post" action="../controllers/update_order_status.php">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <select name="status" class="form-select d-inline w-auto">
                                <?php
                                $statuses = ['Mới', 'Đã xác nhận', 'Đang giao hàng', 'Đã giao', 'Đã hủy'];
                                foreach ($statuses as $s) {
                                    $selected = $order['status'] === $s ? 'selected' : '';
                                    echo "<option value='$s' $selected>$s</option>";
                                }
                                ?>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Cập nhật</button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
        <a href="../admin/admin_orders.php" class="btn btn-secondary">← Quay lại</a>
    </div>

    <script>
        setTimeout(function() {
            const alertBox = document.getElementById('success-alert');
            if (alertBox) {
                alertBox.classList.remove('show');
                alertBox.classList.add('fade');
                alertBox.style.display('none');
            }
        }, 3000);
    </script>
</body>

</html>