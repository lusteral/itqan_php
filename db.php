<?php
$host = "localhost";
$user = "root";
$password = ""; 
$database = "itqan_db"; // تأكد أن هذا هو اسم القاعدة في phpMyAdmin

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>