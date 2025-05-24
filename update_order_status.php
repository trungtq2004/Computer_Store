<?php
$conn = new mysqli('localhost', 'root', '', 'computer_store');
if ($conn->connect_error) die("Lỗi kết nối CSDL");

$order_id = $_POST['order_id'] ?? 0;
$status = $_POST['status'] ?? '';

$valid_statuses = ['Mới', 'Đã xác nhận', 'Đang giao hàng', 'Đã giao', 'Đã hủy'];
if (in_array($status, $valid_statuses)) {
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
}

header("Location: admin_order_detail.php?id=$order_id");
exit;
?>