<?php
session_start();
include "koneksi.php";

$error = "";

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if($result->num_rows > 0){
        $_SESSION['username'] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Username atau password salah, silakan cek kembali!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Autentikasi — Cloud UAS</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;700&family=Space+Mono&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0a0a0f;
            --surface: #14141f;
            --accent: #7c6af7;
            --accent-glow: rgba(124, 106, 247, 0.4);
            --text: #f0eeff;
            --text-muted: #8b8aa0;
            --border: rgba(124, 106, 247, 0.2);
        }

        *, *::before, *::after { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            position: relative;
        }

        .login-container {
            position: relative;
            width: 100%;
            max-width: 400px;
            padding: 20px;
            z-index: 1;
        }

        .login-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 40px 35px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            position: relative;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, var(--accent), #c084fc);
            border-radius: 20px 20px 0 0;
        }

        .brand-logo {
            font-family: 'Space Mono', monospace;
            font-size: 11px;
            color: var(--accent);
            letter-spacing: 0.2em;
            text-transform: uppercase;
            margin-bottom: 12px;
            display: block;
            text-align: center;
        }

        h2 {
            font-family: 'Syne', sans-serif;
            font-size: 26px;
            font-weight: 800;
            text-align: center;
            margin-bottom: 30px;
            letter-spacing: -0.02em;
            color: #ffffff;
        }

        .form-group {
            margin-bottom: 22px;
        }

        label {
            font-family: 'Space Mono', monospace;
            font-size: 11px;
            color: var(--text-muted);
            display: block;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            background: #10101a;
            border: 1px solid var(--border);
            border-radius: 10px;
            color: #fff;
            font-size: 15px;
            transition: all 0.2s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 12px var(--accent-glow);
        }

        button {
            width: 100%;
            padding: 14px;
            background: var(--accent);
            border: none;
            color: #fff;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 15px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 20px rgba(124, 106, 247, 0.3);
            margin-top: 10px;
        }

        button:hover {
            background: #9580ff;
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(124, 106, 247, 0.5);
        }

        .error-box {
            background: rgba(255, 107, 107, 0.1);
            border: 1px solid rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <span class="brand-logo">UAS — Cloud Computing</span>
        <h2>Secure Gateway</h2>

        <?php if($error != ""){ ?>
            <div class="error-box"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST">
            <div class="form-group">
                <label>Identitas / Username</label>
                <input type="text" name="username" placeholder="Masukkan username" autocomplete="off" required>
            </div>

            <div class="form-group">
                <label>Kunci Akses / Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" name="login">AUTHENTICATE</button>
        </form>
    </div>
</div>

</body>
</html>