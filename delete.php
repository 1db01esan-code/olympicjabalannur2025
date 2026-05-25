<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "turnamen_olimpiade";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);
$conn->set_charset('utf8mb4');

// Include regenerasi jadwal
require_once "regenerate_schedules.php";

// =====================
// Validasi parameter
// =====================
if (!isset($_GET['tabel']) || !isset($_GET['id'])) {
    die("❌ Parameter tabel atau id tidak lengkap!");
}

$table = preg_replace("/[^a-zA-Z0-9_]/", "", $_GET['tabel']);
$id    = $_GET['id'];

// Cari primary key tabel
$pk_result = $conn->query("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'");
if (!$pk_result || $pk_result->num_rows == 0) die("Primary key tidak ditemukan!");
$pk_row = $pk_result->fetch_assoc();
$pk_col = $pk_row['Column_name'];

// =====================
// Proses hapus data
// =====================
if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
    $key_safe = $conn->real_escape_string($id);

    // Jika primary key composite (chess: ronde+meja)
    if ($table === "jadwal_chess" && strpos($key_safe, "_") !== false) {
        list($ronde, $meja) = explode("_", $key_safe);
        $sql = "DELETE FROM `$table` WHERE ronde='$ronde' AND meja='$meja'";
    } else {
        $sql = "DELETE FROM `$table` WHERE `$pk_col`='$key_safe' LIMIT 1";
    }

    if ($conn->query($sql) === TRUE) {
        // Regenerasi jadwal setelah hapus peserta
        regenerateAllSchedules($conn);

        header("Location: admin_dashboard.php?deleted=1");
        exit;
    } else {
        $message = "❌ Error: " . $conn->error;
    }
} elseif (isset($_POST['confirm']) && $_POST['confirm'] === 'no') {
    header("Location: admin_dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Konfirmasi Hapus</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f6f8;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}
.modal {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    text-align: center;
    max-width: 400px;
}
h2 {
    margin-bottom: 20px;
    color: #333;
}
.buttons {
    display: flex;
    justify-content: space-around;
    margin-top: 25px;
}
button {
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s;
}
.yes {
    background: #d9534f;
    color: #fff;
}
.yes:hover {
    background: #c9302c;
}
.no {
    background: #6c757d;
    color: #fff;
}
.no:hover {
    background: #5a6268;
}
.message { color: red; margin-bottom: 15px; }
</style>
</head>
<body>
<div class="modal">
    <h2>Yakin anda ingin menghapus data ini?</h2>
    <?php if (!empty($message)): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="buttons">
            <button type="submit" name="confirm" value="yes" class="yes">Ya</button>
            <button type="submit" name="confirm" value="no" class="no">Tidak</button>
        </div>
    </form>
</div>
</body>
</html>