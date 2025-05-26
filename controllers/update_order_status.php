<?php
$conn = new mysqli('localhost', 'root', '', 'computer_store');
if ($conn->connect_error) die("Lỗi kết nối CSDL" . $conn->connect_error);

// Nhận dữ liệu POST
$order_id = $_POST['order_id'] ? (int)$_POST['order_id'] : 0;
$status = $_POST['status'] ?? '';

$valid_statuses = ['Mới', 'Đã xác nhận', 'Đang giao hàng', 'Đã giao', 'Đã hủy'];
if (in_array($status, $valid_statuses)) {

    // Cập nhật trạng thái đơn hàng
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    $stmt->close();
}

// // Lấy email
// $result = $conn->query("SELECT email, customer_name FROM orders WHERE id = $order_id");
// $order = $result->fetch_assoc();
// $email = $order['email'];
// $name = $order['customer_name'];

// // Nội dung email
// $subject = "Cập nhật đơn hàng #$order_id";
// $body = "Chào $name, \n\nTrạng thái đơn hàng $order_id được cập nhật thành: $status.\n\nCảm ơn bạn đã mua hàng tại Computer Store!";

// // Gửi email
// $headers = "From: no-reply@yourdomain.com\r\n";
// mail($email, $subject, $body);

include_once '../PHPMailer/send_mail.php'; // Đường dẫn đến file bạn vừa tạo

// Lấy thông tin khách hàng
$result = $conn->query("SELECT email, customer_name FROM orders WHERE id = $order_id");
$order = $result->fetch_assoc();
$email = $order['email'];
$name = $order['customer_name'];

// Gửi email
sendOrderEmail($email, $name, $order_id, $status);
// Quay lại trang chi tiết đơn hàng
header("Location: ../admin/admin_order_detail.php?id=$order_id&success=1");
exit;
