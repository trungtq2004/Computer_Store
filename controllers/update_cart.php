<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $qty = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = $qty;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
