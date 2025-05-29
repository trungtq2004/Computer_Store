<?php
session_start();
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Lưu thông tin user vào session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];
        // Nếu là admin thì vào trang admin, nếu không về trang chủ
        if ($user['role'] === 'admin') {
            header('Location: ../admin/orders.php');
        } else {
            header('Location: ../index.php');
        }
        exit;
    } else {
        $error = "Email hoặc mật khẩu không đúng!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Đăng nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5 col-md-4">
    <h3>Đăng nhập</h3>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" required class="form-control" />
        </div>
        <div class="mb-3">
            <label>Mật khẩu</label>
            <input type="password" name="password" required class="form-control" />
        </div>
        <button class="btn btn-primary">Đăng nhập</button>
    </form>
</div>
</body>
</html>
