<?php
session_start();
include('../includes/db.php');
if ($_SESSION['role'] !== 'admin') { header("Location: ../auth/login.php"); exit; }

$id = $_GET['id'];
$conn->query("DELETE FROM products WHERE id = $id");

header("Location: products.php");
exit;
