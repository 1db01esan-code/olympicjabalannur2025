<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "turnamen_olimpiade";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Buat tabel jika belum ada
$conn->query("CREATE TABLE IF NOT EXISTS peserta_volleyball (
    rw INT PRIMARY KEY,
    nama_pic VARCHAR(100) NOT NULL,
    telp_pic VARCHAR(20) NOT NULL,
    pemain1 VARCHAR(100) NOT NULL,
    pemain2 VARCHAR(100) NOT NULL,
    pemain3 VARCHAR(100) NOT NULL,
    pemain4 VARCHAR(100) NOT NULL,
    pemain5 VARCHAR(100) NOT NULL,
    pemain6 VARCHAR(100) NOT NULL,
    pemain7 VARCHAR(100) NOT NULL,
    pemain8 VARCHAR(100) NOT NULL,
    pemain9 VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rw = isset($_POST['rw']) ? (int)$_POST['rw'] : 0;
    $nama_pic = $_POST['nama_pic'];
    $telp_pic = $_POST['telp_pic'];
    $pemain = [];
    for ($i = 1; $i <= 9; $i++) {
        $pemain[] = isset($_POST["pemain$i"]) ? $_POST["pemain$i"] : '';
    }

    if ($rw <= 0) {
        $errorMessage = "RW tidak valid!";
    } elseif ($conn->query("SELECT * FROM peserta_volleyball WHERE rw=$rw")->num_rows > 0) {
        $errorMessage = "RW ini sudah terdaftar!";
    } elseif ((int)$conn->query("SELECT COUNT(*) as total FROM peserta_volleyball")->fetch_assoc()['total'] >= 4) {
        $errorMessage = "Kuota sudah penuh! Maksimal 4 tim.";
    } else {
        // Insert peserta
        $stmt = $conn->prepare("INSERT INTO peserta_volleyball 
            (rw, nama_pic, telp_pic, pemain1,pemain2,pemain3,pemain4,pemain5,pemain6,pemain7,pemain8,pemain9)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param(
            "isssssssssss",
            $rw, $nama_pic, $telp_pic,
            $pemain[0], $pemain[1], $pemain[2], $pemain[3], $pemain[4],
            $pemain[5], $pemain[6], $pemain[7], $pemain[8]
        );
        $stmt->execute();
        $stmt->close();

        // Pesan sukses dengan tambahan jadwal main
        $successMessage = "🎉 Selamat! RW $rw berhasil terdaftar. ".
                          "Anda bermain pada hari <strong>Sabtu, 4 & 11 Oktober 2025</strong>. ".
                          "Tetap semangat, jaga sportivitas, dan raih kemenangan dengan fair play!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pendaftaran Bola Voli</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body { margin:0; font-family:'Inter', sans-serif; background:#f4f6f8; color:#333; }
.header {
    background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('images/volleyball.jpg') no-repeat center/cover;
    height: 250px; display:flex; flex-direction:column; align-items:center; justify-content:center; color:white; text-align:center;
}
.header h1 { font-size:2.2rem; margin:0; font-weight:700; text-shadow:2px 2px 8px rgba(0,0,0,0.7); }
.container { max-width:700px; margin:-50px auto 50px auto; background:white; padding:30px; border-radius:12px; box-shadow:0 8px 25px rgba(0,0,0,0.1); }
h2 { color:#ff6b00; margin-bottom:20px; font-size:1.8rem; }
label { display:block; margin-top:12px; font-weight:600; }
input { width:100%; padding:10px; margin-top:6px; border:1px solid #ddd; border-radius:6px; font-size:1rem; }
button { margin-top:20px; padding:12px 25px; background:#ff6b00; color:white; border:none; border-radius:50px; font-weight:600; cursor:pointer; transition:0.3s; font-size:1rem; }
button:hover { background:#e65c00; }

/* Notifikasi sukses */
.success-banner {
    background: #d4edda;
    border-left: 6px solid #28a745;
    color: #155724;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight:600;
}

/* Notifikasi error */
.error-banner {
    background: #f8d7da;
    border-left: 6px solid #dc3545;
    color: #721c24;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight:600;
}

@media(max-width:768px){
    .container { margin:20px 15px; padding:20px; }
    .header h1 { font-size:1.8rem; }
}
</style>
</head>
<body>
<div class="header">
    <h1>Pendaftaran Bola Voli</h1>
</div>
<div class="container">
    <h2>Form Pendaftaran Tim (RW)</h2>

    <?php if(!empty($successMessage)): ?>
        <div class="success-banner"><?= $successMessage ?></div>
    <?php elseif(!empty($errorMessage)): ?>
        <div class="error-banner"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="post">
        <label>RW (Nomor Tim)</label>
        <input type="number" name="rw" required min="1">
        <label>Nama PIC (Penanggung Jawab)</label>
        <input type="text" name="nama_pic" required>
        <label>No. Telp PIC</label>
        <input type="text" name="telp_pic" required>
        <?php for($i=1;$i<=9;$i++): ?>
            <label>Pemain <?= $i ?></label>
            <input type="text" name="pemain<?= $i ?>" required>
        <?php endfor; ?>
        <button type="submit">Daftar Sekarang</button>
    </form>
</div>
</body>
</html>