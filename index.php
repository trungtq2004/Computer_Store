<?php 
session_start();
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$totalItem = array_sum($cart);
?>
<?php
include("includes/dp.php");
?>
<?php
include("includes/header.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Computer Store</title>
</head>
<body>
<h2 class="mb-4"> Sản phẩm nổi bật</h2>
<nav class="navbar navbar-expand-lg navbar-light bg-light px-3">
    <a class="navbar-brand" href="index.php"> Computer Store </a>
    <div class="ms-auto">
        <a href="cart.php" class="btn btn-outline-primary">🛒 Giỏ hàng (<?php echo $totalItem; ?>)</a>
    </div>
</nav>
<div class="row">
    <?php
    $result = mysqli_query($conn,"SELECT * FROM products");
    while ($row = mysqli_fetch_assoc($result)){
        echo "<div class='col-md-3 mb-4'>";
        echo "<div class='card h-100'>";
        echo "<img src='assets/images/{$row['image']}' class='card-img-top' alt='{$row['name']}' ' >";
        echo "<div class='card-body'>";
        echo "<h5 class='card-title'> {$row['name']} </h5>";
        echo "      <p class='card-text'>" . number_format($row['price']) . " VND</p>";
        echo "      <a href='product_detail.php?id={$row['id']}' class='btn btn-primary'>Xem chi tiết</a>";
        echo "    </div>";
        echo "  </div>";
        echo "</div>";
    }
    ?>
</div>
</body>
</html>

<?php
include("includes/footer.php");
?>