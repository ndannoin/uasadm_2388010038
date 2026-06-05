<?php
$host = getenv("DATABASE_HOST") ?: "uas-mariadb"; // Menggunakan DNS internal docker
$user = getenv("MYSQL_USER") ?: "zaidan_user";
$pass = getenv("MYSQL_PASSWORD") ?: "password_zaidan_123";
$db   = getenv("MYSQL_DATABASE") ?: "uas_zaidan_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi Database Gagal: " . $conn->connect_error);
}
?>
