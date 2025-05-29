<?php
session_start();
include('../includes/db.php');

// Chỉ cho người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy sản phẩm trong giỏ của user
$sql = "SELECT c.id, c.quantity, p.name, p.price, p.image
        FROM cart_items c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

    <h2>🛒 Giỏ hàng của bạn</h2>

    <?php if ($result->num_rows === 0): ?>
        <p>Không có sản phẩm nào trong giỏ.</p>
    <?php else: ?>
    <form action="update-cart.php" method="post">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th>Xóa</th>
                </tr>
            </thead>
            <tbody>
                <?php while($item = $result->fetch_assoc()): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                <tr>
                    <td><img src="../uploads/<?= $item['image'] ?>" width="80"></td>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                    <td>
                        <input type="number" name="quantities[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" min="1" class="form-control" style="width: 80px;">
                    </td>
                    <td><?= number_format($subtotal, 0, ',', '.') ?>đ</td>
                    <td><a href="remove-item.php?id=<?= $item['id'] ?>" class="btn btn-danger btn-sm">X</a></td>
                </tr>
                <?php endwhile; ?>
                <tr>
                    <td colspan="4" class="text-end"><strong>Tổng tiền:</strong></td>
                    <td colspan="2"><strong><?= number_format($total, 0, ',', '.') ?>đ</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="d-flex justify-content-between">
            <a href="../index.php" class="btn btn-secondary">⬅ Tiếp tục mua</a>
            <div>
                <button type="submit" class="btn btn-warning">🔄 Cập nhật giỏ hàng</button>
                <a href="../checkout.php" class="btn btn-success">💳 Thanh toán</a>
            </div>
        </div>
    </form>
    <?php endif; ?>

</body>
</html>
