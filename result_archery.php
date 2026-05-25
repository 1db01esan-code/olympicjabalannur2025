<?php
// Koneksi DB
$host = "localhost";
$user = "root";
$pass = "";
$db   = "turnamen_olimpiade";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Buat tabel hasil_archery jika belum ada
$conn->query("CREATE TABLE IF NOT EXISTS hasil_archery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ronde VARCHAR(50) NOT NULL,
    pemain1 VARCHAR(100) NOT NULL,
    pemain2 VARCHAR(100) NOT NULL,
    skor_p1 INT DEFAULT 0,
    skor_p2 INT DEFAULT 0,
    status VARCHAR(20) DEFAULT 'Belum Selesai',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// Ambil data hasil pertandingan
$res = $conn->query("SELECT * FROM hasil_archery ORDER BY id ASC");
$hasil = [];
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $hasil[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Hasil Pertandingan Panahan</title>
  <style>
    body { font-family: Arial, sans-serif; background:#fafafa; margin:0; padding:20px; }
    .container { max-width:900px; margin:auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,.1); }
    h2 { text-align:center; color:#007bff; }
    table { width:100%; border-collapse:collapse; margin-top:20px; }
    th, td { border:1px solid #ddd; padding:10px; text-align:center; }
    th { background:#f9f9f9; }
    .status { font-weight:bold; }
    .finished { color:green; }
    .pending { color:red; }
    .back { display:block; text-align:center; margin-top:20px; text-decoration:none; color:#007bff; }
    .back:hover { text-decoration:underline; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Hasil Pertandingan Panahan</h2>

    <?php if (empty($hasil)): ?>
      <p style="text-align:center; color:#777;">Belum ada hasil pertandingan yang dicatat.</p>
    <?php else: ?>
      <table>
        <tr>
          <th>Ronde</th>
          <th>Pemain 1</th>
          <th>Skor</th>
          <th>Pemain 2</th>
          <th>Status</th>
          <th>Update Terakhir</th>
        </tr>
        <?php foreach ($hasil as $h): ?>
          <tr>
            <td><?= htmlspecialchars($h['ronde']) ?></td>
            <td><?= htmlspecialchars($h['pemain1']) ?></td>
            <td><?= (int)$h['skor_p1'] ?> - <?= (int)$h['skor_p2'] ?></td>
            <td><?= htmlspecialchars($h['pemain2']) ?></td>
            <td class="status <?= $h['status']=='Selesai'?'finished':'pending' ?>">
              <?= htmlspecialchars($h['status']) ?>
            </td>
            <td><?= htmlspecialchars($h['updated_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>

    <a href="archery.php" class="back">← Kembali ke Halaman Panahan</a>
  </div>
</body>
</html>
<?php $conn->close(); ?>