<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
include('../includes/db.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $price = (int)$_POST['price'];

    if (empty($name) || $price <= 0) {
        $error = "Tên sản phẩm và giá phải hợp lệ.";
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $error = "Vui lòng chọn ảnh sản phẩm hợp lệ.";
    } else {
        // Xử lý file ảnh
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];

        if (!in_array($file_type, $allowed_types)) {
            $error = "Chỉ chấp nhận file ảnh định dạng JPG, PNG, GIF.";
        } else {
            $filename = time() . "_" . basename($_FILES['image']['name']);
            $filename = preg_replace('/[^A-Za-z0-9_.-]/', '_', $filename);
            $target_path = "../uploads/" . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $stmt = $conn->prepare("INSERT INTO products (name, price, image) VALUES (?, ?, ?)");
                $stmt->bind_param("sis", $name, $price, $filename);

                if ($stmt->execute()) {
                    $success = "Thêm sản phẩm thành công!";
                } else {
                    $error = "Lỗi khi thêm sản phẩm vào cơ sở dữ liệu.";
                    // Nếu cần bạn có thể xóa file đã upload khi lỗi db xảy ra
                    unlink($target_path);
                }
            } else {
                $error = "Không thể tải file lên máy chủ.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Thêm sản phẩm - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5" style="max-width:600px;">
    <h3>Thêm sản phẩm mới</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Tên sản phẩm</label>
            <input type="text" id="name" name="name" class="form-control" required value="<?= isset($name) ? htmlspecialchars($name) : '' ?>" />
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Giá (VNĐ)</label>
            <input type="number" id="price" name="price" class="form-control" required min="1" value="<?= isset($price) ? (int)$price : '' ?>" />
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Ảnh sản phẩm</label>
            <input type="file" id="image" name="image" class="form-control" required accept="image/*" />
        </div>
        <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
        <a href="../admin/orders.php" class="btn btn-secondary ms-2">Quay lại danh sách</a>
    </form>
</div>
</body>
</html>
