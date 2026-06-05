<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = getenv("DATABASE_HOST") ?: "uas-mariadb";
$user = getenv("MYSQL_USER") ?: "zaidan_user";
$pass = getenv("MYSQL_PASSWORD") ?: "password_zaidan_123";
$db   = getenv("MYSQL_DATABASE") ?: "uas_zaidan_db";

try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $error) {
    http_response_code(500);
    die("Koneksi Database Gagal: " . htmlspecialchars($error->getMessage(), ENT_QUOTES, "UTF-8"));
}

$conn->query("
    CREATE TABLE IF NOT EXISTS users (
        id INT(11) NOT NULL AUTO_INCREMENT,
        username VARCHAR(50) NOT NULL,
        password VARCHAR(255) NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY username_unique (username)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
");

$conn->query("
    CREATE TABLE IF NOT EXISTS berita (
        id INT(11) NOT NULL AUTO_INCREMENT,
        judul VARCHAR(255) NOT NULL,
        kategori VARCHAR(100) NOT NULL,
        isi TEXT NOT NULL,
        tanggal_dibuat DATETIME NOT NULL,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
");

$defaultUsername = "admin";
$defaultPassword = "admin 123";
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $defaultUsername);
$stmt->execute();
$adminUser = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($adminUser) {
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
    $stmt->bind_param("ss", $defaultPassword, $defaultUsername);
    $stmt->execute();
    $stmt->close();
} else {
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $defaultUsername, $defaultPassword);
    $stmt->execute();
    $stmt->close();
}
?>
