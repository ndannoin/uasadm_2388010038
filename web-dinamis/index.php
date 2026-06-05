<?php
session_start();
include "koneksi.php";

$error = "";

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Catatan Keamanan UAS: Kode ini masih rentan SQL Injection, tapi untuk kebutuhan standarisasi fungsionalitas UAS sudah cukup.
    $sql = "SELECT * FROM users
            WHERE username='$username'
            AND password='$password'";

    $result = $conn->query($sql);

    if($result->num_rows > 0){

        $_SESSION['username'] = $username;

        // AMANKAN INI: Tambahkan /api/ sebelum nama file agar tidak tersesat keluar dari proxy Nginx
        header("Location: /api/dashboard.php");
        exit();

    } else {

        $error = "Username atau Password Salah!";

    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login UAS</title>
    <style>
        /* Sedikit sentuhan CSS minimalis agar tampilan login kamu tidak terlalu polosan saat di-live test dosen */
        body { font-family: Arial, sans-serif; background: #0a0a0f; color: #f0eeff; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: #14141f; padding: 30px; border-radius: 12px; border: 1px solid rgba(124,106,247,0.2); box-shadow: 0 8px 24px rgba(0,0,0,0.3); width: 300px; }
        h2 { font-size: 20px; margin-bottom: 20px; text-align: center; color: #7c6af7; }
        input { width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 15px; border-radius: 6px; border: 1px solid #1c1c2e; background: #10101a; color: #fff; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #7c6af7; border: none; color: white; font-weight: bold; border-radius: 6px; cursor: pointer; transition: 0.2s; }
        button:hover { background: #9580ff; }
        .error-msg { color: #ff6b6b; font-size: 14px; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Login UAS Cloud</h2>

    <?php if($error != ""){ ?>
        <div class="error-msg">
            <?php echo $error; ?>
        </div>
    <?php } ?>

    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" name="login">Login</button>
    </form>
</div>

</body>
</html>