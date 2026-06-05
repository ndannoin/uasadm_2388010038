<?php
$host = "uas-mariadb"; // WAJIB memanggil nama service di docker-compose
$user = "admin";
$pass = "admin123";
$db   = "uasadm_2388010038";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi ke MariaDB Gagal: " . $conn->connect_error);
}
?>