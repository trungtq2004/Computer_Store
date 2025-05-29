<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID sản phẩm không hợp lệ.";
    exit;
}

$id = (int)$_GET['id'];
$product = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();

if (!$product) {
    echo "Sản phẩm không tồn tại.";
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $price = (int)$_POST['price'];

    // Validate input
    if (empty($name)) {
        $error = "Tên sản phẩm không được để trống.";
    } elseif ($price <= 0) {
        $error = "Giá sản phẩm phải lớn hơn 0.";
    } else {
        if (!empty($_FILES['image']['name'])) {
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed_types)) {
                $error = "Chỉ cho phép upload ảnh với định dạng: jpg, jpeg, png, gif.";
            } else {
                $image = time() . '_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/$image")) {
                    if ($product['image'] && file_exists("../uploads/" . $product['image'])) {
                        unlink("../uploads/" . $product['image']);
                    }
                    $sql = "UPDATE products SET name='$name', price=$price, image='$image' WHERE id=$id";
                    if ($conn->query($sql)) {
                        $success = "Cập nhật sản phẩm thành công.";
                        $product['name'] = $name;
                        $product['price'] = $price;
                        $product['image'] = $image;
                    } else {
                        $error = "Lỗi cập nhật: " . $conn->error;
                    }
                } else {
                    $error = "Lỗi khi tải ảnh lên.";
                }
            }
        } else {
            $sql = "UPDATE products SET name='$name', price=$price WHERE id=$id";
            if ($conn->query($sql)) {
                $success = "Cập nhật sản phẩm thành công.";
                $product['name'] = $name;
                $product['price'] = $price;
            } else {
                $error = "Lỗi cập nhật: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sửa sản phẩm - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 25px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgb(149 157 165 / 0.2);
        }
        .img-preview {
            max-width: 180px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="form-container shadow-sm">
    <h3 class="mb-4 text-center">Sửa sản phẩm</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div><?= $error ?></div>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div><?= $success ?></div>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Tên sản phẩm</label>
            <input
                type="text"
                id="name"
                name="name"
                class="form-control"
                placeholder="Nhập tên sản phẩm"
                required
                value="<?= htmlspecialchars($product['name']) ?>"
            />
        </div>

        <div class="mb-3">
            <label for="price" class="form-label fw-semibold">Giá (VNĐ)</label>
            <input
                type="number"
                id="price"
                name="price"
                class="form-control"
                placeholder="Nhập giá sản phẩm"
                min="1"
                required
                value="<?= (int)$product['price'] ?>"
            />
        </div>

        <div class="mb-3">
            <label for="image" class="form-label fw-semibold">Ảnh sản phẩm</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*" />
            <?php if ($product['image'] && file_exists("../uploads/" . $product['image'])): ?>
                <img
                    src="../uploads/<?= htmlspecialchars($product['image']) ?>"
                    alt="<?= htmlspecialchars($product['name']) ?>"
                    class="img-preview"
                />
            <?php else: ?>
                <p class="text-muted mt-2">Chưa có ảnh</p>
            <?php endif; ?>
        </div>

        <div class="d-flex justify-content-between">
            <a href="products.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Lưu thay đổi
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
