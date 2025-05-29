<?php
include('includes/db.php');
session_start();

if (!isset($_GET['id'])) {
    echo "Không có sản phẩm nào được chọn.";
    exit;
}

$id = (int)$_GET['id'];
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.id = $id";

$result = $conn->query($sql);
$product = $result->fetch_assoc();

if (!$product) {
    echo "Sản phẩm không tồn tại.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?> - Chi tiết sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <!-- Ảnh sản phẩm -->
        <div class="col-md-5">
            <img src="uploads/<?= htmlspecialchars($product['image']) ?>" class="img-fluid border rounded" alt="<?= htmlspecialchars($product['name']) ?>">
        </div>

        <!-- Thông tin sản phẩm -->
        <div class="col-md-7">
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <p><span class="badge bg-primary">Danh mục: <?= htmlspecialchars($product['category_name']) ?></span></p>
            <h4 class="text-danger"><?= number_format($product['price'], 0, ',', '.') ?>đ</h4>
            
            <p><strong>Tình trạng:</strong> 
                <?= ($product['status'] ?? 1) ? '<span class="text-success">Còn hàng</span>' : '<span class="text-danger">Hết hàng</span>' ?>
            </p>

            <p><strong>Mô tả:</strong></p>
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>

            <hr>
            <form action="cart/add-to-cart.php" method="POST" class="row g-2">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <div class="col-4">
                    <input type="number" name="quantity" value="1" min="1" class="form-control">
                </div>
                <div class="col-8 d-grid gap-2 d-md-block">
                    <button type="submit" class="btn btn-success">🛒 Thêm vào giỏ</button>
                    <a href="index.php" class="btn btn-secondary">⬅ Quay lại</a>
                </div>
            </form>

            <hr>
            <div>
                <h5>⭐ Đánh giá (giả lập):</h5>
                <p>
                    ⭐⭐⭐⭐☆ (4/5)<br>
                    "Sản phẩm tốt, đúng mô tả, giao hàng nhanh." <br>
                    <small class="text-muted">– Khách hàng A</small>
                </p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
