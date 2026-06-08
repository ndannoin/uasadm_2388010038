<?php
session_start();
include "koneksi.php";

if (isset($_SESSION["username"])) {
    header("Location: dashboard.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare("SELECT username, password FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user && hash_equals($user["password"], $password)) {
        session_regenerate_id(true);
        $_SESSION["username"] = $user["username"];
        header("Location: dashboard.php");
        exit();
    }

    $message = "Username atau password salah.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — Cloud UAS</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;700&family=Space+Mono&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0a0a0f;
            --surface: #14141f;
            --accent: #7c6af7;
            --accent2: #c084fc;
            --text: #f0eeff;
            --muted: #9b99ad;
            --border: rgba(124, 106, 247, 0.25);
            --error: #ff6b6b;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background:
                radial-gradient(circle at top left, rgba(124, 106, 247, 0.2), transparent 35%),
                radial-gradient(circle at bottom right, rgba(192, 132, 252, 0.16), transparent 35%),
                var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            padding: 42px 34px;
            border: 1px solid var(--border);
            border-radius: 24px;
            background: rgba(20, 20, 31, 0.92);
            box-shadow: 0 28px 80px rgba(0, 0, 0, 0.55);
        }

        .brand {
            display: block;
            margin-bottom: 12px;
            color: var(--accent2);
            font: 700 11px 'Space Mono', monospace;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            text-align: center;
        }

        h1 {
            margin-bottom: 10px;
            font: 800 32px 'Syne', sans-serif;
            text-align: center;
        }

        .subtitle {
            margin-bottom: 30px;
            color: var(--muted);
            text-align: center;
            line-height: 1.6;
        }

        .alert {
            margin-bottom: 18px;
            padding: 12px;
            border: 1px solid rgba(255, 107, 107, 0.3);
            border-radius: 12px;
            background: rgba(255, 107, 107, 0.1);
            color: var(--error);
            text-align: center;
            font-size: 14px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--muted);
            font: 700 11px 'Space Mono', monospace;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        input {
            width: 100%;
            margin-bottom: 18px;
            padding: 15px 16px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: #10101a;
            color: #fff;
            font: 500 15px 'DM Sans', sans-serif;
            outline: none;
        }

        input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(124, 106, 247, 0.16);
        }

        button {
            width: 100%;
            margin-top: 6px;
            padding: 15px;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            color: #fff;
            cursor: pointer;
            font: 800 14px 'Syne', sans-serif;
            letter-spacing: 0.04em;
        }

        .hint {
            margin-top: 18px;
            color: var(--muted);
            text-align: center;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <main class="login-card">
        <span class="brand">UAS Cloud Computing</span>
        <h1>Admin Login</h1>
        <p class="subtitle">Masuk untuk mengelola data berita pada dashboard CRUD.</p>

        <?php if ($message !== "") { ?>
            <div class="alert"><?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?></div>
        <?php } ?>

        <form method="POST" action="index.php">
            <label for="username">Username</label>
            <input id="username" name="username" type="text" value="admin" autocomplete="username" required>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>

            <button type="submit">Masuk 
                board</button>
        </form>

        <p class="hint">Akun default: admin / admin 123</p>
    </main>
</body>
</html>
