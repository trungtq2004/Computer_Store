<?php
include('../admin/check-admin.php');
include('../includes/db.php');

$sql = "SELECT orders.id, users.name, orders.total, orders.status, orders.created_at 
        FROM orders 
        JOIN users ON orders.user_id = users.id 
        ORDER BY orders.created_at DESC";
$result = $conn->query($sql);

function statusBadge($status) {
    return match ($status) {
        'Mới' => '<span class="badge bg-primary">Mới</span>',
        'Đã xác nhận' => '<span class="badge bg-info text-dark">Đã xác nhận</span>',
        'Đang giao hàng' => '<span class="badge bg-warning text-dark">Đang giao hàng</span>',
        'Đã giao' => '<span class="badge bg-success">Đã giao</span>',
        'Đã hủy' => '<span class="badge bg-danger">Đã hủy</span>',
        default => '<span class="badge bg-secondary">Không rõ</span>',
    };
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>🛎️ Quản lý Đơn hàng - Hoàng cung Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; font-size: 1.5rem; }
        .table thead th { vertical-align: middle; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand" href="#">Hoàng cung Admin</a>
    <div class="ms-auto">
        <a href="../index.php" class="btn btn-outline-light me-2">Trang chủ</a>
        <a href="../admin/products.php" class="btn btn-outline-light me-2">Quản lý sản phẩm</a>
        <a href="../auth/logout.php" class="btn btn-outline-light">Đăng xuất</a>
    </div>
</nav>

<div class="container mt-5">
    <h3 class="mb-4 text-center text-primary">📋 Danh sách Đơn hàng</h3>

    <?php if ($result->num_rows === 0): ?>
        <div class="alert alert-warning text-center">Chưa có đơn hàng nào.</div>
    <?php else: ?>
    <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle text-center">
        <thead class="table-dark">
            <tr>
                <th>Mã đơn</th>
                <th>Khách hàng</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td>#<?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td class="text-danger fw-bold"><?= number_format($row['total'], 0, ',', '.') ?>đ</td>
                <td><?= statusBadge($row['status']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                <td>
                    <a href="order-detail.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm" title="Xem chi tiết đơn hàng">
                        <i class="bi bi-eye"></i> Xem
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>

<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

</body>
</html>
