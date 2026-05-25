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

// Buat tabel hasil_basketball jika belum ada
$conn->query("CREATE TABLE IF NOT EXISTS hasil_basketball (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pertandingan VARCHAR(50) NOT NULL,
    tim1 VARCHAR(100) NOT NULL,
    tim2 VARCHAR(100) NOT NULL,
    skor_tim1 INT DEFAULT 0,
    skor_tim2 INT DEFAULT 0,
    status VARCHAR(20) DEFAULT 'Belum Selesai',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// Ambil data hasil pertandingan
$res = $conn->query("SELECT * FROM hasil_basketball ORDER BY id ASC");
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
  <title>Hasil Pertandingan - Bola Basket</title>
  <style>
    body { font-family: Arial, sans-serif; background:#fafafa; margin:0; padding:20px; }
    .container { max-width:900px; margin:auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,.1); }
    h2 { text-align:center; color:#dc2430; }
    table { width:100%; border-collapse:collapse; margin-top:20px; }
    th, td { border:1px solid #ddd; padding:10px; text-align:center; }
    th { background:#f9f9f9; }
    .status { font-weight:bold; }
    .finished { color:green; }
    .pending { color:red; }
    .back { display:block; text-align:center; margin-top:20px; text-decoration:none; color:#dc2430; }
    .back:hover { text-decoration:underline; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Hasil Pertandingan Bola Basket</h2>

    <?php if (empty($hasil)): ?>
      <p style="text-align:center; color:#777;">Belum ada hasil pertandingan yang dicatat.</p>
    <?php else: ?>
      <table>
        <tr>
          <th>Pertandingan</th>
          <th>Tim 1</th>
          <th>Skor</th>
          <th>Tim 2</th>
          <th>Status</th>
          <th>Update Terakhir</th>
        </tr>
        <?php foreach ($hasil as $h): ?>
          <tr>
            <td><?= htmlspecialchars($h['pertandingan']) ?></td>
            <td><?= htmlspecialchars($h['tim1']) ?></td>
            <td><?= (int)$h['skor_tim1'] ?> - <?= (int)$h['skor_tim2'] ?></td>
            <td><?= htmlspecialchars($h['tim2']) ?></td>
            <td class="status <?= $h['status']=='Selesai'?'finished':'pending' ?>">
              <?= htmlspecialchars($h['status']) ?>
            </td>
            <td><?= htmlspecialchars($h['updated_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>

    <a href="basketball.php" class="back">← Kembali ke Halaman Basket</a>
  </div>
</body>
</html>
<?php $conn->close(); ?>