<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}
include('../includes/db.php');

$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Quản lý sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-4">
    <h2>Quản lý sản phẩm</h2>
    <a href="add_product.php" class="btn btn-success mb-3">+ Thêm sản phẩm</a>
    <a href="orders.php" class="btn btn-exit mb-3">Trang chủ</a>
    <table class="table table-bordered align-middle text-center">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Tên sản phẩm</th>
                <th>Giá (VNĐ)</th>
                <th>Ảnh</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= number_format($row['price'], 0, ',', '.') ?> đ</td>
                <td>
                    <?php if ($row['image'] && file_exists("../uploads/" . $row['image'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" width="100" alt="<?= htmlspecialchars($row['name']) ?>" />
                    <?php else: ?>
                        <span class="text-muted">Không có ảnh</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                    <a href="delete_product.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
