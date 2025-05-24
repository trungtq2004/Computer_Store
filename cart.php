<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'computer_store');
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$productData = [];


if (!empty($cart)) {
    $ids = implode(',', array_keys($cart));
    $sql = "SELECT * FROM products WHERE id IN ($ids)";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $productData[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Giỏ hàng</title>
</head>

<body>
    <div class="container py-4">
        <h2>Giỏ hàng của tôi</h2>
        <?php if (empty($cart)) { ?>
            <div class="alert alert-warning">Giỏ hàng trống.</div>
            <a href="index.php" class="btn btn-primary"> <- Quay về trang chủ</a>
                <?php } else { ?>
                    <form method='post' action='cart.php'>
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Hình ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Tổng</th>
                                    <th>Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total = 0;
                                foreach ($productData as $product) {
                                    $qty = $cart[$product['id']];
                                    $subtotal = $qty * $product['price'];
                                    $total += $subtotal;
                                    $image = "assets/images/" . $product['image'];

                                    echo "
                      
                        <tr>
                            <td>    <img class='rounded' src='$image' width='80' height='80'> </td>
                            <td>    {$product['name']} </td>
                            <td>    " . number_format($product['price']) . " VND </td>
                            <td>    <input type='number' name='quantity[{$product['id']}]'
                                    value='$qty' min='1' 
                                    class='form-control quantity-input' 
                                    data-id='{$product['id']}' 
                                    style='width:80px;'> </td>
                            <td>    " . number_format($subtotal) . " VND </td>
                            <td>
                                    <a href='remove_cart.php?id={$product['id']}'
                                    class='btn btn-sm btn-danger'
                                    onclick=\"return confirm('Bạn có chắc muốn xóa sản phẩm này không?');\"> X </a>
                            </td>
                        </tr> 
                    ";
                                }


                                ?>
                                <tr class="table-light fw-bold">
                                    <th colspan="4">Tổng cộng</th>
                                    <th id="cart-total"> <?php echo number_format($total); ?> VND</th>
                                </tr>
                                <script>
                                    document.querySelectorAll("input[type='number']").forEach(input => {
                                        input.addEventListener("change", function() {
                                            const id = this.name.match(/\d+/)[0];
                                            const qty = parseInt(this.value);

                                            // Gửi AJAX để cập nhật session
                                            fetch('update_cart.php', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/x-www-form-urlencoded'
                                                    },
                                                    body: `id=${id}&qty=${qty}`
                                                })
                                                .then(res => res.json())
                                                .then(data => {
                                                    if (data.success) {
                                                        // Cập nhật lại tổng từng dòng và tổng cộng
                                                        const row = this.closest('tr');
                                                        const priceText = row.querySelector('td:nth-child(3)').textContent;
                                                        const price = parseInt(priceText.replace(/\D/g, ''));
                                                        const subtotal = qty * price;
                                                        row.querySelector('td:nth-child(5)').textContent = subtotal.toLocaleString() + ' VND';

                                                        // Tính lại tổng cộng
                                                        let total = 0;
                                                        document.querySelectorAll("tbody tr").forEach(tr => {
                                                            const stCell = tr.querySelector('td:nth-child(5)');
                                                            if (stCell) {
                                                                const st = parseInt(stCell.textContent.replace(/\D/g, ''));
                                                                if (!isNaN(st)) total += st;
                                                            }
                                                        });
                                                        document.getElementById('cart-total').textContent = total.toLocaleString() + ' VND';
                                                    }
                                                });
                                        });
                                    });
                                </script>
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-between mt-3">
                            <a href="index.php" class="btn btn-secondary">← Tiếp tục mua hàng</a>
                            <button type="submit" formaction="checkout.php" class="btn btn-success">Thanh toán</button>
                        </div>
                    <?php    }  ?>
    </div>




</body>

</html>