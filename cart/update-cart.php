<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: view-cart.php");
    exit;
}

$user_id = $_SESSION['user_id'];

foreach ($_POST['quantities'] as $cart_item_id => $quantity) {
    $quantity = (int)$quantity;
    if ($quantity > 0) {
        $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("iii", $quantity, $cart_item_id, $user_id);
        $stmt->execute();
    }
}

header("Location: view-cart.php");
exit;
