<?php
$host = "uas-mariadb"; // Menggunakan DNS internal docker
$user = "zaidan_user";
$pass = "password_zaidan_123";
$db   = "uas_zaidan_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi Database Gagal: " . $conn->connect_error);
}
?>