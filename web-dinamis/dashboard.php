<?php
session_start();
// Proteksi halaman: jika belum login, kembalikan ke index.php
if(!isset($_SESSION['username'])){
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Cloud — UAS</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;700&family=Space+Mono&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0a0a0f;
            --surface: #14141f;
            --accent: #38bdf8; /* Warna cyan cerah untuk nuansa sukses */
            --text: #f0eeff;
            --text-muted: #8b8aa0;
            --border: rgba(56, 189, 248, 0.15);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .dashboard-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 24px;
            max-width: 600px;
            width: 100%;
            padding: 40px;
            box-shadow: 0 30px 70px rgba(0,0,0,0.6);
            text-align: center;
            position: relative;
        }

        .status-badge {
            display: inline-block;
            font-family: 'Space Mono', monospace;
            font-size: 11px;
            background: rgba(56, 189, 248, 0.1);
            color: var(--accent);
            padding: 6px 16px;
            border-radius: 40px;
            border: 1px solid var(--border);
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        h1 {
            font-family: 'Syne', sans-serif;
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 16px;
            letter-spacing: -0.03em;
        }

        p.welcome-text {
            color: var(--text-muted);
            font-size: 16px;
            margin-bottom: 35px;
            font-weight: 300;
        }

        .system-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 40px;
        }

        .grid-item {
            background: #10101a;
            border: 1px solid rgba(255,255,255,0.03);
            border-radius: 14px;
            padding: 20px;
            text-align: left;
        }

        .grid-label {
            font-family: 'Space Mono', monospace;
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .grid-value {
            font-size: 15px;
            font-weight: 500;
            color: #fff;
        }

        .logout-btn {
            display: inline-block;
            padding: 12px 32px;
            background: transparent;
            border: 1px solid rgba(255, 107, 107, 0.3);
            color: #ff6b6b;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .logout-btn:hover {
            background: rgba(255, 107, 107, 0.08);
            border-color: #ff6b6b;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

<div class="dashboard-card">
    <div class="status-badge">● Environment Active</div>
    <h1>Access Granted</h1>
    <p class="welcome-text">Selamat datang kembali di pusat kendali infrastruktur cloud node.</p>

    <div class="system-grid">
        <div class="grid-item">
            <div class="grid-label">Authenticated User</div>
            <div class="grid-value">@<?php echo htmlspecialchars($_SESSION['username']); ?></div>
        </div>
        <div class="grid-item">
            <div class="grid-label">Server Database</div>
            <div class="grid-value">MariaDB 11 (Healthy)</div>
        </div>
        <div class="grid-item">
            <div class="grid-label">Host Node</div>
            <div class="grid-value">AWS EC2 Instance</div>
        </div>
        <div class="grid-item">
            <div class="grid-label">Deployment Mode</div>
            <div class="grid-value">Docker Containers</div>
        </div>
    </div>

    <a href="logout.php" class="logout-btn">TERMINATE SESSION</a>
</div>

</body>
</html>