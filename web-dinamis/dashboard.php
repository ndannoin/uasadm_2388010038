<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit();
}

include "koneksi.php";

$message = $_SESSION["message"] ?? "";
$status = $_SESSION["status"] ?? "";
unset($_SESSION["message"], $_SESSION["status"]);

$idBerita = "";
$judulEdit = "";
$kategoriEdit = "";
$isiEdit = "";
$isEditMode = false;

if (isset($_GET["action"], $_GET["id"]) && $_GET["action"] === "edit") {
    $idBerita = (int) $_GET["id"];
    $stmt = $conn->prepare("SELECT id, judul, kategori, isi FROM berita WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $idBerita);
    $stmt->execute();
    $dataEdit = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($dataEdit) {
        $judulEdit = $dataEdit["judul"];
        $kategoriEdit = $dataEdit["kategori"];
        $isiEdit = $dataEdit["isi"];
        $isEditMode = true;
    }
}

if (isset($_GET["action"], $_GET["id"]) && $_GET["action"] === "delete") {
    $idBerita = (int) $_GET["id"];
    $stmt = $conn->prepare("DELETE FROM berita WHERE id = ?");
    $stmt->bind_param("i", $idBerita);
    $stmt->execute();
    $deleted = $stmt->affected_rows > 0;
    $stmt->close();

    $_SESSION["status"] = $deleted ? "success" : "error";
    $_SESSION["message"] = $deleted ? "Berita berhasil dihapus." : "Berita tidak ditemukan.";
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["simpan_berita"])) {
    $judulBerita = trim($_POST["judul_berita"] ?? "");
    $kategori = trim($_POST["kategori"] ?? "");
    $isiBerita = trim($_POST["isi_berita"] ?? "");
    $idUpdate = (int) ($_POST["id_berita"] ?? 0);

    if ($judulBerita === "" || $kategori === "" || $isiBerita === "") {
        $_SESSION["status"] = "error";
        $_SESSION["message"] = "Semua field wajib diisi.";
    } elseif ($idUpdate > 0) {
        $stmt = $conn->prepare("UPDATE berita SET judul = ?, kategori = ?, isi = ? WHERE id = ?");
        $stmt->bind_param("sssi", $judulBerita, $kategori, $isiBerita, $idUpdate);
        $stmt->execute();
        $updated = $stmt->affected_rows >= 0;
        $stmt->close();

        $_SESSION["status"] = $updated ? "success" : "error";
        $_SESSION["message"] = $updated ? "Berita berhasil diperbarui." : "Gagal memperbarui berita.";
    } else {
        $tanggal = date("Y-m-d H:i:s");
        $stmt = $conn->prepare("INSERT INTO berita (judul, kategori, isi, tanggal_dibuat) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $judulBerita, $kategori, $isiBerita, $tanggal);
        $stmt->execute();
        $inserted = $stmt->affected_rows > 0;
        $stmt->close();

        $_SESSION["status"] = $inserted ? "success" : "error";
        $_SESSION["message"] = $inserted ? "Berita baru berhasil diterbitkan." : "Gagal menambahkan berita.";
    }

    header("Location: dashboard.php");
    exit();
}

$result = $conn->query("SELECT id, judul, kategori, tanggal_dibuat FROM berita ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard CRUD Berita — Cloud UAS</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;700&family=Space+Mono&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0a0a0f;
            --surface: #14141f;
            --surface2: #1c1c2e;
            --accent: #7c6af7;
            --accent2: #c084fc;
            --text: #f0eeff;
            --muted: #9b99ad;
            --border: rgba(124, 106, 247, 0.25);
            --success: #10b981;
            --error: #ff6b6b;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            padding: 34px 18px;
            background:
                radial-gradient(circle at top left, rgba(124, 106, 247, 0.18), transparent 34%),
                radial-gradient(circle at bottom right, rgba(192, 132, 252, 0.12), transparent 34%),
                var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
        }

        .dashboard {
            width: 100%;
            max-width: 1050px;
            margin: 0 auto;
            display: grid;
            gap: 28px;
        }

        .topbar, .card, .table-card {
            border: 1px solid var(--border);
            border-radius: 22px;
            background: rgba(20, 20, 31, 0.94);
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.45);
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 22px 26px;
        }

        .brand {
            color: var(--accent2);
            font: 700 11px 'Space Mono', monospace;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        h1 {
            margin-top: 6px;
            font: 800 30px 'Syne', sans-serif;
        }

        .user {
            color: var(--muted);
            font-size: 14px;
        }

        .logout {
            display: inline-block;
            padding: 11px 18px;
            border: 1px solid rgba(255, 107, 107, 0.34);
            border-radius: 12px;
            color: var(--error);
            text-decoration: none;
            font: 700 13px 'Syne', sans-serif;
        }

        .content-grid {
            display: grid;
            grid-template-columns: minmax(280px, 420px) 1fr;
            gap: 28px;
            align-items: start;
        }

        .card, .table-card {
            padding: 28px;
        }

        h2 {
            margin-bottom: 20px;
            font: 800 24px 'Syne', sans-serif;
        }

        .alert {
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 14px;
        }

        .alert.success {
            border: 1px solid rgba(16, 185, 129, 0.3);
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .alert.error {
            border: 1px solid rgba(255, 107, 107, 0.3);
            background: rgba(255, 107, 107, 0.1);
            color: var(--error);
        }

        label {
            display: block;
            margin: 0 0 8px;
            color: var(--muted);
            font: 700 11px 'Space Mono', monospace;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        input, select, textarea {
            width: 100%;
            margin-bottom: 18px;
            padding: 13px 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: #10101a;
            color: #fff;
            font: 500 14px 'DM Sans', sans-serif;
            outline: none;
        }

        textarea {
            min-height: 150px;
            resize: vertical;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(124, 106, 247, 0.14);
        }

        .button-row {
            display: flex;
            gap: 10px;
        }

        button, .cancel {
            flex: 1;
            padding: 13px 16px;
            border: 0;
            border-radius: 12px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            font: 800 13px 'Syne', sans-serif;
        }

        button {
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            color: #fff;
        }

        .cancel {
            background: var(--surface2);
            color: var(--muted);
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 560px;
        }

        th, td {
            padding: 14px 12px;
            border-bottom: 1px solid rgba(124, 106, 247, 0.14);
            text-align: left;
            vertical-align: top;
        }

        th {
            color: var(--muted);
            font: 700 11px 'Space Mono', monospace;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .badge {
            display: inline-block;
            padding: 5px 9px;
            border: 1px solid rgba(192, 132, 252, 0.3);
            border-radius: 999px;
            background: rgba(192, 132, 252, 0.12);
            color: var(--accent2);
            font-size: 12px;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .action {
            padding: 7px 10px;
            border-radius: 9px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
        }

        .edit {
            background: rgba(124, 106, 247, 0.14);
            color: var(--accent2);
        }

        .delete {
            background: rgba(255, 107, 107, 0.12);
            color: var(--error);
        }

        .empty {
            color: var(--muted);
            text-align: center;
        }

        @media (max-width: 850px) {
            .content-grid { grid-template-columns: 1fr; }
            .topbar { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
    <main class="dashboard">
        <section class="topbar">
            <div>
                <span class="brand">UAS Cloud Computing 2388010038</span>
                <h1>Dashboard CRUD Berita</h1>
                <p class="user">Login sebagai @<?php echo htmlspecialchars($_SESSION["username"], ENT_QUOTES, "UTF-8"); ?></p>
            </div>
            <a class="logout" href="logout.php">Logout</a>
        </section>

        <section class="content-grid">
            <div class="card">
                <h2><?php echo $isEditMode ? "Edit Berita" : "Tambah Berita"; ?></h2>

                <?php if ($message !== "") { ?>
                    <div class="alert <?php echo htmlspecialchars($status, ENT_QUOTES, "UTF-8"); ?>">
                        <?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?>
                    </div>
                <?php } ?>

                <form method="POST" action="dashboard.php">
                    <input type="hidden" name="id_berita" value="<?php echo htmlspecialchars((string) $idBerita, ENT_QUOTES, "UTF-8"); ?>">

                    <label for="judul_berita">Judul Berita</label>
                    <input id="judul_berita" type="text" name="judul_berita" value="<?php echo htmlspecialchars($judulEdit, ENT_QUOTES, "UTF-8"); ?>" autocomplete="off" required>

                    <label for="kategori">Kategori</label>
                    <select id="kategori" name="kategori" required>
                        <option value="" disabled <?php echo $kategoriEdit === "" ? "selected" : ""; ?>>Pilih kategori...</option>
                        <?php
                        $categories = ["Cloud Computing", "Teknologi", "Cyber Security", "Akademik"];
                        foreach ($categories as $category) {
                            $selected = $kategoriEdit === $category ? "selected" : "";
                            echo "<option value=\"" . htmlspecialchars($category, ENT_QUOTES, "UTF-8") . "\" $selected>" . htmlspecialchars($category, ENT_QUOTES, "UTF-8") . "</option>";
                        }
                        ?>
                    </select>

                    <label for="isi_berita">Isi Berita</label>
                    <textarea id="isi_berita" name="isi_berita" required><?php echo htmlspecialchars($isiEdit, ENT_QUOTES, "UTF-8"); ?></textarea>

                    <div class="button-row">
                        <button type="submit" name="simpan_berita"><?php echo $isEditMode ? "Update Data" : "save"; ?></button>
                        <?php if ($isEditMode) { ?>
                            <a class="cancel" href="dashboard.php">Batal</a>
                        <?php } ?>
                    </div>
                </form>
            </div>

            <div class="table-card">
                <h2>Daftar Berita</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $id = (int) $row["id"];
                                    echo "<tr>";
                                    echo "<td>" . $no++ . "</td>";
                                    echo "<td><strong>" . htmlspecialchars($row["judul"], ENT_QUOTES, "UTF-8") . "</strong></td>";
                                    echo "<td><span class=\"badge\">" . htmlspecialchars($row["kategori"], ENT_QUOTES, "UTF-8") . "</span></td>";
                                    echo "<td>" . htmlspecialchars($row["tanggal_dibuat"], ENT_QUOTES, "UTF-8") . "</td>";
                                    echo "<td class=\"actions\">";
                                    echo "<a class=\"action edit\" href=\"dashboard.php?action=edit&id=$id\">Edit</a>";
                                    echo "<a class=\"action delete\" href=\"dashboard.php?action=delete&id=$id\" onclick=\"return confirm('Hapus berita ini?')\">Hapus</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td class=\"empty\" colspan=\"5\">Belum ada berita yang diterbitkan.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
