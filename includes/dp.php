<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "computer_store";

$conn = mysqli_connect($host, $user, $pass, $db);
if(!$conn){
    die("kết nối thất bại: " .mysqli_connect_error());
}


?>