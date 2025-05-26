<?php   
    use PHPMailer\PHPMailer\PHPMailer; 
    use PHPMailer\PHPMailer\Exception;

    require'../PHPMailer/PHPMailer.php';
    require'../PHPMailer/SMTP.php';
    require'../PHPMailer/Exception.php';

    function sendOrderEmail($to,$name,$order_id,$status){
        $mail = new PHPMailer(true);
 try {
        // Cấu hình SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Gmail SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'zangtv.k4@gmail.com'; // Thay bằng Gmail bạn
        $mail->Password = 'ehxf fgch pftx tvbb'; // Mật khẩu ứng dụng Gmail
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Người gửi
        $mail->setFrom('zangtv.k4@gmail.com', 'Computer Store');

        // Người nhận
        $mail->addAddress($to, $name);

        // Nội dung
        $mail->isHTML(true);
        $mail->Subject = "Cập nhật đơn hàng #$order_id";
        $mail->Body = "Chào <strong>$name</strong>,<br><br>Trạng thái đơn hàng <strong>#$order_id</strong> của bạn đã được cập nhật thành: <strong>$status</strong>.<br><br>Cảm ơn bạn đã mua hàng tại <strong>Computer Store</strong>!";
        $mail->AltBody = "Chào $name,\n\nTrạng thái đơn hàng #$order_id của bạn đã được cập nhật thành: $status.\n\nCảm ơn bạn đã mua hàng tại Computer Store!";

        $mail->send();
        // echo 'Email đã được gửi';
    } catch (Exception $e) {
        // echo "Gửi email thất bại. Lỗi: {$mail->ErrorInfo}";
    }
}

?>