<?php 
session_start();
include('includes/db.php');

// Lấy danh mục
$categories = $conn->query("SELECT * FROM categories");

// Xử lý lọc và tìm kiếm
$filter = '';
if (!empty($_GET['category_id'])) {
    $category_id = (int)$_GET['category_id'];
    $filter .= " AND category_id = $category_id";
}
if (!empty($_GET['keyword'])) {
    $keyword = $conn->real_escape_string($_GET['keyword']);
    $filter .= " AND name LIKE '%$keyword%'";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ - Máy Tính Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light px-5">
    <a class="navbar-brand" href="index.php">🖥️ Máy Tính Store</a>
    <div class="ms-auto">
        <?php if (isset($_SESSION['user_name'])): ?>
            Xin chào, <strong><?= $_SESSION['user_name'] ?></strong> |
            <a href="cart/view-cart.php" class="btn btn-sm btn-outline-primary">Giỏ hàng</a> |
            <a href="auth/logout.php">Đăng xuất</a>
        <?php else: ?>
            <a href="auth/login.php">Đăng nhập</a> |
            <a href="auth/register.php">Đăng ký</a>
        <?php endif; ?>
    </div>
</nav>

<!-- Banner -->
<div class="container mt-4">
    <div class="bg-primary text-white p-4 rounded text-center">
        <h1>Chào mừng đến với Máy Tính Store 🖥️</h1>
        <p class="lead">Nơi bạn tìm thấy những chiếc máy tính phù hợp với nhu cầu!</p>
    </div>
</div>

<!-- Bộ lọc và tìm kiếm -->
<div class="container mt-4">
    <form class="row g-3 mb-4" method="GET">
        <div class="col-md-4">
            <select name="category_id" class="form-select">
                <option value="">-- Tất cả danh mục --</option>
                <?php while($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>" <?= (isset($category_id) && $cat['id'] == $category_id) ? 'selected' : '' ?>>
                        <?= $cat['name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-6">
            <input type="text" name="keyword" class="form-control" placeholder="Tìm theo tên sản phẩm..." value="<?= $_GET['keyword'] ?? '' ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Tìm kiếm</button>
        </div>
    </form>
</div>

<!-- Danh sách sản phẩm -->
<div class="container">
    <h3 class="mb-4 text-center">🛒 Sản phẩm mới nhất</h3>
    <div class="row">
        <?php
        $sql = "SELECT * FROM products WHERE 1 $filter ORDER BY id DESC";
        $products = $conn->query($sql);
        if ($products->num_rows > 0):
            while($row = $products->fetch_assoc()):
        ?>
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                <img src="uploads/<?= $row['image'] ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                    <p class="text-danger fw-bold"><?= number_format($row['price'], 0, ',', '.') ?>đ</p>
                    <a href="product-detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary w-100">🔍 Xem chi tiết</a>
                </div>
            </div>
        </div>
        <?php endwhile; else: ?>
            <p class="text-center">Không tìm thấy sản phẩm phù hợp.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
