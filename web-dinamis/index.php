<?php
session_start();
include "koneksi.php";

$message = "";
$status = "";

// 1. PROSES BERSIHKAN ID (Aman dari SQL Injection)
$id_berita = "";
$judul_edit = "";
$kategori_edit = "";
$isi_edit = "";
$is_edit_mode = false;

// 2. AKSI: AMBIL DATA UNTUK EDIT (UBAH)
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id_berita = mysqli_real_escape_string($conn, $_GET['id']);
    $res = $conn->query("SELECT * FROM berita WHERE id='$id_berita'");
    if ($res->num_rows > 0) {
        $data_edit = $res->fetch_assoc();
        $judul_edit = $data_edit['judul'];
        $kategori_edit = $data_edit['kategori'];
        $isi_edit = $data_edit['isi'];
        $is_edit_mode = true;
    }
}

// 3. AKSI: HAPUS DATA (HAPUS)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_berita = mysqli_real_escape_string($conn, $_GET['id']);
    if ($conn->query("DELETE FROM berita WHERE id='$id_berita'") === TRUE) {
        $status = "success";
        $message = "Berita berhasil dihapus!";
    } else {
        $status = "error";
        $message = "Gagal menghapus berita: " . $conn->error;
    }
}

// 4. AKSI: SIMPAN DATA (TAMBAH / UPDATE)
if (isset($_POST['simpan_berita'])) {
    $judul_berita = mysqli_real_escape_string($conn, $_POST['judul_berita']);
    $kategori     = mysqli_real_escape_string($conn, $_POST['kategori']);
    $isi_berita   = mysqli_real_escape_string($conn, $_POST['isi_berita']);
    $tanggal      = date('Y-m-d H:i:s');

    if (isset($_POST['id_berita']) && $_POST['id_berita'] != "") {
        // PROSES UPDATE
        $id_update = mysqli_real_escape_string($conn, $_POST['id_berita']);
        $sql = "UPDATE berita SET judul='$judul_berita', kategori='$kategori', isi='$isi_berita' WHERE id='$id_update'";
        if ($conn->query($sql) === TRUE) {
            $status = "success";
            $message = "Berita berhasil diperbarui!";
            // Reset form mode
            $is_edit_mode = false;
        } else {
            $status = "error";
            $message = "Gagal memperbarui berita: " . $conn->error;
        }
    } else {
        // PROSES INSERT (TAMBAH)
        $sql = "INSERT INTO berita (judul, kategori, isi, tanggal_dibuat) VALUES ('$judul_berita', '$kategori', '$isi_berita', '$tanggal')";
        if ($conn->query($sql) === TRUE) {
            $status = "success";
            $message = "Berita baru berhasil diterbitkan!";
        } else {
            $status = "error";
            $message = "Gagal menambahkan berita: " . $conn->error;
        }
    }
}
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
            --accent-glow: rgba(124, 106, 247, 0.4);
            --text: #f0eeff;
            --text-muted: #8b8aa0;
            --border: rgba(124, 106, 247, 0.2);
            --success: #10b981;
            --error: #ff6b6b;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .dashboard-container {
            width: 100%;
            max-width: 900px;
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        .content-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 40px 35px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            position: relative;
        }

        .content-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, var(--accent), var(--accent2));
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
            color: #ffffff;
        }

        .form-group { margin-bottom: 22px; }

        label {
            font-family: 'Space Mono', monospace;
            font-size: 11px;
            color: var(--text-muted);
            display: block;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        input, select, textarea {
            width: 100%;
            padding: 14px 16px;
            background: #10101a;
            border: 1px solid var(--border);
            border-radius: 10px;
            color: #fff;
            font-size: 15px;
            transition: all 0.2s ease;
            box-sizing: border-box;
            font-family: 'DM Sans', sans-serif;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 12px var(--accent-glow);
        }

        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%238b8aa0' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'></polyline></svg>");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 16px;
        }

        textarea { resize: vertical; min-height: 120px; }

        .btn-container { display: flex; gap: 12px; margin-top: 10px; }

        button, .btn-cancel {
            flex: 1;
            padding: 14px;
            border: none;
            color: #fff;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 14px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            text-decoration: none;
        }

        button {
            background: var(--accent);
            box-shadow: 0 4px 20px rgba(124, 106, 247, 0.3);
        }

        button:hover { background: #9580ff; transform: translateY(-2px); }

        .btn-cancel { background: var(--surface2); border: 1px solid rgba(255,255,255,0.05); color: var(--text-muted); }
        .btn-cancel:hover { color: #fff; background: #252538; }

        /* STYLES TABEL DATA BERITA */
        .table-wrapper {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 30px;
            overflow-x: auto;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }

        .table-title {
            font-family: 'Syne', sans-serif;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        th {
            font-family: 'Space Mono', monospace;
            font-size: 11px;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 12px 16px;
            border-bottom: 2px solid var(--border);
        }

        td { padding: 16px; border-bottom: 1px solid rgba(124, 106, 247, 0.1); color: var(--text); }
        tr:hover td { background: rgba(124, 106, 247, 0.03); }

        .badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-family: 'Space Mono', monospace;
            background: rgba(192, 132, 252, 0.15);
            color: var(--accent2);
            border: 1px solid rgba(192, 132, 252, 0.3);
        }

        .actions { display: flex; gap: 8px; }
        .btn-action {
            padding: 6px 12px;
            font-size: 12px;
            font-family: 'Space Mono', monospace;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        .btn-edit { background: rgba(124, 106, 247, 0.1); color: var(--accent); border-color: rgba(124, 106, 247, 0.2); }
        .btn-edit:hover { background: var(--accent); color: #fff; }
        .btn-delete { background: rgba(255, 107, 107, 0.1); color: var(--error); border-color: rgba(255, 107, 107, 0.2); }
        .btn-delete:hover { background: var(--error); color: #fff; }

        .alert-box { padding: 12px; border-radius: 8px; font-size: 13px; text-align: center; margin-bottom: 20px; }
        .alert-box.success { background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: var(--success); }
        .alert-box.error { background: rgba(255, 107, 107, 0.1); border: 1px solid rgba(255, 107, 107, 0.2); color: var(--error); }
    </style>
</head>
<body>

<div class="dashboard-container">
    
    <div class="content-card">
        <span class="brand-logo">UAS — Cloud Computing 2388010038</span>
        <h2><?php echo $is_edit_mode ? "Update News Data" : "Publish News"; ?></h2>

        <?php if($message != ""){ ?>
            <div class="alert-box <?php echo $status; ?>"><?php echo $message; ?></div>
        <?php } ?>

        <form method="POST" action="index.php"> <input type="hidden" name="id_berita" value="<?php echo $id_berita; ?>">

            <div class="form-group">
                <label>Judul Berita</label>
                <input type="text" name="judul_berita" placeholder="Masukkan judul utama berita" value="<?php echo $judul_edit; ?>" autocomplete="off" required>
            </div>

            <div class="form-group">
                <label>Kategori Berita</label>
                <select name="kategori" required>
                    <option value="" disabled <?php if($kategori_edit == "") echo "selected"; ?>>Pilih Kategori...</option>
                    <option value="Cloud Computing" <?php if($kategori_edit == "Cloud Computing") echo "selected"; ?>>Cloud Computing</option>
                    <option value="Teknologi" <?php if($kategori_edit == "Teknologi") echo "selected"; ?>>Teknologi Informasi</option>
                    <option value="Cyber Security" <?php if($kategori_edit == "Cyber Security") echo "selected"; ?>>Cyber Security</option>
                    <option value="Akademik" <?php if($kategori_edit == "Akademik") echo "selected"; ?>>Akademik / Kampus</option>
                </select>
            </div>

            <div class="form-group">
                <label>Isi Dokumen Berita</label>
                <textarea name="isi_berita" placeholder="Tuliskan narasi berita secara lengkap di sini..." required><?php echo $isi_edit; ?></textarea>
            </div>

            <div class="btn-container">
                <button type="submit" name="simpan_berita">
                    <?php echo $is_edit_mode ? "UPDATE DATA" : "PUBLISH NEWS"; ?>
                </button>
                <?php if ($is_edit_mode) { ?>
                    <a href="index.php" class="btn-cancel">BATAL</a>
                <?php } ?>
            </div>
        </form>
    </div>

    <div class="table-wrapper">
        <div class="table-title">Daftar Berita Terbuka</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Judul Berita</th>
                    <th style="width: 150px;">Kategori</th>
                    <th style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $result = $conn->query("SELECT * FROM berita ORDER BY id DESC");
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td><strong>" . htmlspecialchars($row['judul']) . "</strong></td>";
                        echo "<td><span class='badge'>" . htmlspecialchars($row['kategori']) . "</span></td>";
                        echo "<td class='actions'>
                                <a href='index.php?action=edit&id=" . $row['id'] . "' class='btn-action btn-edit'>Ubah</a>
                                <a href='index.php?action=delete&id=" . $row['id'] . "' class='btn-action btn-delete' onclick='return confirm(\"Hapus berita ini?\")'>Hapus</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' style='text-align: center; color: var(--text-muted);'>Belum ada berita yang diterbitkan.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>