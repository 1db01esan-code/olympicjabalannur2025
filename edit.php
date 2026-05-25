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
$conn->set_charset("utf8mb4");

// ===================
// Validasi parameter
// ===================
if (!isset($_GET['tabel']) || !isset($_GET['id'])) {
    die("Parameter tabel atau id tidak lengkap!");
}

$table = preg_replace("/[^a-zA-Z0-9_]/", "", $_GET['tabel']);
$id    = $_GET['id'];

// Cari primary key
$pk_result = $conn->query("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'");
if (!$pk_result || $pk_result->num_rows == 0) die("Primary key tidak ditemukan!");
$pk_row = $pk_result->fetch_assoc();
$pk_col = $pk_row['Column_name'];

// Ambil data lama
$stmt = $conn->prepare("SELECT * FROM `$table` WHERE `$pk_col`=? LIMIT 1");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) die("Data tidak ditemukan!");
$data = $result->fetch_assoc();
$stmt->close();

// ===================
// Update jika submit
// ===================
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [];
    $values = [];
    $types  = "";

    foreach ($data as $col => $oldVal) {
        if ($col == $pk_col) continue; // jangan update PK
        if (isset($_POST[$col])) {
            $fields[] = "`$col`=?";
            $values[] = $_POST[$col];
            $types   .= "s";
        }
    }

    if (!empty($fields)) {
        $sql = "UPDATE `$table` SET " . implode(", ", $fields) . " WHERE `$pk_col`=?";
        $stmt = $conn->prepare($sql);
        $values[] = $id;
        $types   .= "s";
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            header("Location: admin_dashboard.php?updated=1");
            exit;
        } else {
            $message = "❌ Gagal update: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Data</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, sans-serif;
        background: #f4f6f8;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
    }
    .card {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        width: 450px;
        max-width: 95%;
    }
    h2 {
        margin-bottom: 20px;
        color: #2c3e50;
        text-align: center;
    }
    .form-group {
        margin-bottom: 15px;
    }
    label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
        color: #555;
    }
    input[type="text"], input[type="number"], input[type="date"], input[type="time"], select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }
    .buttons {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
    }
    button, a {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        text-decoration: none;
        text-align: center;
    }
    .save {
        background: #27ae60;
        color: #fff;
    }
    .save:hover { background: #219150; }
    .cancel {
        background: #e74c3c;
        color: #fff;
    }
    .cancel:hover { background: #c0392b; }
    .message {
        margin-bottom: 15px;
        padding: 10px;
        border-radius: 6px;
        background: #f8d7da;
        color: #721c24;
    }
</style>
</head>
<body>
<div class="card">
    <h2>Edit Data - <?= htmlspecialchars($table) ?></h2>

    <?php if (!empty($message)): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <form method="post">
        <?php foreach ($data as $col => $val): ?>
            <?php if ($col == $pk_col) continue; ?>
            <div class="form-group">
                <label><?= htmlspecialchars($col) ?></label>
                <input type="text" name="<?= htmlspecialchars($col) ?>" value="<?= htmlspecialchars($val) ?>">
            </div>
        <?php endforeach; ?>

        <div class="buttons">
            <button type="submit" class="save">💾 Simpan</button>
            <a href="admin_dashboard.php" class="cancel">✖ Batal</a>
        </div>
    </form>
</div>
</body>
</html>