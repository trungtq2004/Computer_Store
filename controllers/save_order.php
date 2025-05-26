<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'computer_store');
if ($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

// Lấy dữ liệu khách hàng
$name    = trim($_POST['customer_name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$cart    = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo "Giỏ hàng trống";
    exit;
}

if (empty($name) || empty($email) || empty($phone) || empty($address)) {
    echo "Vui lòng điền đầy đủ thông tin.";
    exit;
}

// Lấy thông tin sản phẩm từ giỏ hàng
$total_price = 0;
$product_ids = implode(',', array_keys($cart));
$result = $conn->query("SELECT * FROM products WHERE id IN ($product_ids)");

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[$row['id']] = $row;
    $total_price += $cart[$row['id']] * $row['price'];
}

// Lưu đơn hàng
$stmt = $conn->prepare("INSERT INTO orders (customer_name, email, phone, address, total_price) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssd", $name, $email, $phone, $address, $total_price);
$stmt->execute();
$order_id = $stmt->insert_id;
$stmt->close();

// Lưu chi tiết đơn hàng
$stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
foreach ($cart as $product_id => $quantity) {
    $price = $products[$product_id]['price'];
    $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
    $stmt->execute();
}
$stmt->close();

// Xóa giỏ hàng
unset($_SESSION['cart']);

// Chuyển tới trang xác nhận
header("Location: ../views/order_success.php?order_id=$order_id");
exit;
