<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: view-cart.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_item_id = (int)$_GET['id'];

$stmt = $conn->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $cart_item_id, $user_id);
$stmt->execute();

header("Location: view-cart.php");
exit;
