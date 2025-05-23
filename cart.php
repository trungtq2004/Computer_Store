<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'computer_store');
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$productData = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $id => $qty) {
        $id = (int)$id;
        $qty = max(1, (int)$qty);
        $_SESSION['cart'][$id] = $qty;
    }
    header("Location: cart.php");
    exit;
}

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
    <?php if(empty($cart)){ ?>
        <p> Giỏ hàng trống </p>
    <?php }else { ?>
          <form method='post' action='cart.php'>
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tổng</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                foreach($productData as $product){
                    $qty = $cart[$product['id']];
                    $subtotal = $qty * $product['price'];
                    $total += $subtotal;
                    $image ="assets/images/" . $product['image'];

                    echo "
                      
                        <tr>
                            <td> <img class='rounded' src='$image' width='80' height='80'> </td>
                            <td> {$product['name']} </td>
                            <td>" . number_format($product['price']) . " VND </td>
                            <td> <input type='number' name='quantity[{$product['id']}]'
                             value='$qty' min='1' class='form-control' style='width:80px;'> </td>
                            <td>" . number_format($subtotal) . " VND </td>
                           
                        </tr> 
                    ";  
                }
                   

                ?>
                <tr class="table-light fw-bold">
                    <th colspan="4">Tổng cộng</th>
                    <th><?php echo number_format($total); ?> VND</th>
                </tr>
            </tbody>
        </table>
            <div class="d-flex justify-content-between mt-3">
            <a href="index.php" class="btn btn-secondary">← Tiếp tục mua hàng</a>
            <button type="submit" name="update_cart" class="btn btn-success">Cập nhật giỏ hàng</button>
            </div>
<?php    }  ?>
   </div> 
</body>
</html>