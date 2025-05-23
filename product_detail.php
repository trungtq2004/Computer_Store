<?php 
session_start();

$conn = new mysqli('localhost', 'root', '', 'computer_store');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Lấy id sản phẩm từ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Truy vấn sản phẩm
$sql = "SELECT * FROM products WHERE id = $id";
$result = $conn->query($sql);
$product = $result->fetch_assoc();

// Xử lý thêm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = max(1, (int)$_POST['quantity']);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Nếu đã có sản phẩm trong giỏ thì cộng thêm số lượng
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    ob_start();

    echo "<div class='alert alert-success mt-3 d-flex justify-content-between align-items-start' id='cartAlert'>";
    echo "<div>";
    echo "<strong> Đẫ thêm sản phẩm vào giỏ hàng </strong><br>";
    echo "<ul>";
    foreach ($_SESSION['cart'] as $pid => $qty) {
       $p = $conn->query("SELECT name FROM products WHERE id = $pid") -> fetch_assoc();
       echo "<li>{$p['name']} : $qty cái </li>";
    }

    echo "</ul>";
    echo "</div>";
    echo "<a href='cart.php' class='btn btn-sm btn-light ms-3 mt-2'>🛒 Giỏ hàng </a>";
    echo "</div>";
    ob_end_flush();    
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php echo $product['name']; ?> </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container py-4">
        <a class="btn btn-secondary mb-3" href="index.php"><- Quay lại </a>
        <div class="row">
            <div class="col-md-5">
                <img class="img-fluid" src="assets/images/<?php echo $product['image']; ?> "alt="<?php echo $product['name']; ?>">
            </div>
            <div class="col-md-7">
                <h2><?php echo $product['name']; ?></h2>
                <p class="text-danger fw-bold fs-4"> <?php echo number_format($product['price']); ?> VND</p>
                <p><?php echo $product['description']; ?></p>

                <form method="post" action="">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <div class="mb-2">
                        <table>Số lượng: </table>
                        <input type="number" name="quantity" value="1" min="1" class="form-control" style="width:100px" required>
                    </div>
                    <button type="submit" name="add_to_cart" class="btn btn-primary ">Thêm vào giỏ hàng</button>
                    
                </form>
                
                
            </div>
        </div>
    </div>

    <script>
        setTimeout(() => {
            const alertBox = document.getElementById('cartAlert');
            if(alertBox){
                alertBox.style.transition = "opacity 0.5s ease";
                alertBox.style.opacity = 0;
                setTimeout(() => alertBox.remove(), 500);
            }
        },5000);
    </script>

</body>
</html>