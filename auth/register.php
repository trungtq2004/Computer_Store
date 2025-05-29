<?php
session_start();
include('../includes/db.php');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Kiểm tra email đã tồn tại chưa
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errors[] = "Email đã được sử dụng.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users(name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['user_name'] = $name;
            header("Location: ../index.php");
            exit;
        } else {
            $errors[] = "Đăng ký thất bại. Vui lòng thử lại.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5 col-md-6">
    <h3>Đăng ký tài khoản</h3>
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <?= implode("<br>", $errors) ?>
        </div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label>Họ tên</label>
            <input type="text" name="name" required class="form-control">
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" required class="form-control">
        </div>
        <div class="mb-3">
            <label>Mật khẩu</label>
            <input type="password" name="password" required class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Đăng ký</button>
        <a href="login.php" class="btn btn-link">Đã có tài khoản? Đăng nhập</a>
    </form>
</div>
</body>
</html>
