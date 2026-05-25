<?php
// Debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

$MAX_TEAMS = 4;

// koneksi DB
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'turnamen_olimpiade';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // cek kuota
    $res = $conn->query("SELECT COUNT(*) AS cnt FROM peserta_soccer");
    $cnt = ($res) ? (int)$res->fetch_assoc()['cnt'] : 0;
    if ($cnt >= $MAX_TEAMS) {
        echo "<h2>⚠️ Pendaftaran ditutup</h2><p>Kuota sudah penuh ($MAX_TEAMS tim).</p>";
        exit;
    }

    // Ambil input
    $rw       = (int)($_POST['rw'] ?? 0);
    $nama_pic = trim($_POST['nama_pic'] ?? '');
    $telp     = trim($_POST['telp'] ?? '');
    $pemain   = $_POST['pemain'] ?? [];

    if (!is_array($pemain)) $pemain = [];
    $pemain = array_pad(array_map('trim', $pemain), 14, null); // isi sampai 14 slot

    // Validasi minimal
    $pemain_valid = array_filter($pemain, fn($v) => $v !== null && $v !== '');
    if ($rw === 0 || $nama_pic === '' || $telp === '' || count($pemain_valid) < 10) {
        echo "<h2>⚠️ Data tidak lengkap</h2><p>Isi RW, Nama PIC, Telp, dan minimal 10 pemain.</p>";
        exit;
    }

    // Query insert
    $sql = "INSERT INTO peserta_soccer 
    (rw, nama_pic, telp,
    pemain1, pemain2, pemain3, pemain4, pemain5, pemain6, pemain7,
    pemain8, pemain9, pemain10, pemain11, pemain12, pemain13, pemain14, waktu_pendaftaran)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())";

    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Prepare gagal: " . $conn->error);

    $stmt->bind_param(
        "issssssssssssssss",   // 17 huruf total (1 i + 16 s)
        $rw, $nama_pic, $telp,
        $pemain[0], $pemain[1], $pemain[2], $pemain[3], $pemain[4],
        $pemain[5], $pemain[6], $pemain[7], $pemain[8], $pemain[9],
        $pemain[10], $pemain[11], $pemain[12], $pemain[13]
    );

    if ($stmt->execute()) {
        echo "<!DOCTYPE html>
        <html lang='id'>
        <head>
          <meta charset='UTF-8'>
          <title>Tim Terdaftar</title>
          <style>
            body{font-family:'Segoe UI',sans-serif;background:#f4f9f4;padding:40px}
            .card{background:#fff;padding:30px;border-radius:12px;max-width:700px;margin:auto;
                  box-shadow:0 6px 18px rgba(0,0,0,0.1)}
            h2{color:#28a745;text-align:center}
            ul{columns:2;-webkit-columns:2;-moz-columns:2}
          </style>
        </head>
        <body>
          <div class='card'>
            <h2>✅ Tim RW {$rw} berhasil terdaftar!</h2>
            <p><b>PIC:</b> {$nama_pic} ({$telp})</p>
            <h3>Daftar Pemain:</h3>
            <ul>";
            foreach ($pemain_valid as $p) {
                echo "<li>".htmlspecialchars($p)."</li>";
            }
        echo "</ul>
            <p style='text-align:center;margin-top:20px'>Semoga sukses & junjung sportivitas!</p>
	    <p style='text-align:center;margin-top:20px'>Anda bermain pada tanggal 6, 13 & 14 September 2025</p>
            <p style='text-align:center'><a href='soccer.php'>&larr; Kembali ke Halaman Sepak Bola</a></p>
          </div>
        </body>
        </html>";
    } else {
        echo "Gagal simpan data: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>

<!-- FORM HTML -->
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pendaftaran Tim Mini Soccer</title>
  <style>
    body{font-family:'Segoe UI',sans-serif;background:#eef2f7;margin:0;padding:40px}
    form{max-width:800px;margin:auto;background:#fff;padding:30px;border-radius:12px;
         box-shadow:0 6px 18px rgba(0,0,0,0.1)}
    h2{text-align:center;color:#2c3e50}
    label{display:block;margin-top:12px;font-weight:bold;color:#34495e}
    input{width:100%;padding:10px;margin-top:5px;border:1px solid #ccc;border-radius:8px}
    .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:15px;margin-top:15px}
    button{margin-top:25px;width:100%;padding:14px;background:#28a745;color:#fff;
           border:none;border-radius:8px;font-size:16px;cursor:pointer}
    button:hover{background:#218838}
    .back{display:block;text-align:center;margin-top:20px;color:#007bff;text-decoration:none}
  </style>
</head>
<body>
  <form method="POST">
    <h2>Pendaftaran Tim Mini Soccer</h2>
    <label>Asal RW:</label>
    <input type="number" name="rw" required>

    <label>Nama Penanggung Jawab (PIC):</label>
    <input type="text" name="nama_pic" required>

    <label>No. Telepon PIC:</label>
    <input type="text" name="telp" required>

    <h3>Daftar Pemain (minimal 10, maksimal 14)</h3>
    <div class="grid">
      <?php for ($i=1; $i<=14; $i++): ?>
        <div>
          <label>Pemain <?= $i ?><?= $i <= 10 ? ' *' : '' ?>:</label>
          <input type="text" name="pemain[]" <?= $i <= 10 ? 'required' : '' ?>>
        </div>
      <?php endfor; ?>
    </div>

    <button type="submit">Daftarkan Tim</button>
    <a href="soccer.php" class="back">&larr; Kembali</a>
  </form>
</body>
</html>