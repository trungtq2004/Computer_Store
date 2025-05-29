
<?php
session_start();
include('../includes/db.php');

// Phải đăng nhập mới thêm vào giỏ hàng
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
   $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = (int)$_POST['quantity'];

    // Kiểm tra sản phẩm có tồn tại
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if ($product) {
        // Kiểm tra nếu sản phẩm đã có trong giỏ => cập nhật số lượng
        $check = $conn->prepare("SELECT * FROM cart_items WHERE user_id = ? AND product_id = ?");
        $check->bind_param("ii", $user_id, $product_id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            // Tăng số lượng
            $update = $conn->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
            $update->bind_param("iii", $quantity, $user_id, $product_id);
            $update->execute();
        } else {
            // Thêm mới
            $insert = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $insert->bind_param("iii", $user_id, $product_id, $quantity);
            $insert->execute();
        }

        header("Location: ../cart/view-cart.php");
        exit;
    } else {
        echo "Sản phẩm không tồn tại.";
    }
}
?>

