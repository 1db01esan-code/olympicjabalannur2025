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

// Buat tabel hasil_chess jika belum ada
$conn->query("CREATE TABLE IF NOT EXISTS hasil_chess (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ronde INT NOT NULL,
    meja INT NOT NULL,
    pemain1 VARCHAR(100) NOT NULL,
    pemain2 VARCHAR(100) NOT NULL,
    skor_p1 FLOAT DEFAULT 0,
    skor_p2 FLOAT DEFAULT 0,
    status VARCHAR(20) DEFAULT 'Belum Selesai',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// Ambil semua hasil pertandingan
$res = $conn->query("SELECT * FROM hasil_chess ORDER BY ronde ASC, meja ASC");
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
  <title>Hasil Pertandingan Catur</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f5f5f5; margin:0; padding:20px; }
    .container { max-width:950px; margin:auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,.1); }
    h2 { text-align:center; color:#007bff; }
    table { width:100%; border-collapse:collapse; margin-top:20px; }
    th, td { border:1px solid #ddd; padding:10px; text-align:center; }
    th { background:#f9f9f9; }
    .status { font-weight:bold; }
    .finished { color:green; }
    .pending { color:red; }
    .back { display:block; text-align:center; margin-top:20px; text-decoration:none; color:#007bff; font-weight:bold; }
    .back:hover { text-decoration:underline; }
    .round-title { margin-top:30px; font-size:20px; color:#444; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Hasil Pertandingan Catur</h2>

    <?php if (empty($hasil)): ?>
      <p style="text-align:center; color:#777;">Belum ada hasil pertandingan yang dicatat.</p>
    <?php else: ?>
      <?php
      $rondeSekarang = 0;
      foreach ($hasil as $h):
        if ($h['ronde'] != $rondeSekarang):
          if ($rondeSekarang != 0) echo "</table>";
          echo "<div class='round-title'>Ronde " . $h['ronde'] . "</div>";
          echo "<table><tr>
                  <th>Meja</th>
                  <th>Pemain 1</th>
                  <th>Skor</th>
                  <th>Pemain 2</th>
                  <th>Status</th>
                  <th>Update Terakhir</th>
                </tr>";
          $rondeSekarang = $h['ronde'];
        endif;
      ?>
        <tr>
          <td><?= (int)$h['meja'] ?></td>
          <td><?= htmlspecialchars($h['pemain1']) ?></td>
          <td><?= $h['skor_p1'] ?> - <?= $h['skor_p2'] ?></td>
          <td><?= htmlspecialchars($h['pemain2']) ?></td>
          <td class="status <?= $h['status']=='Selesai'?'finished':'pending' ?>">
            <?= htmlspecialchars($h['status']) ?>
          </td>
          <td><?= htmlspecialchars($h['updated_at']) ?></td>
        </tr>
      <?php endforeach; ?>
      </table>
    <?php endif; ?>

    <a href="chess.php" class="back">← Kembali ke Halaman Catur</a>
  </div>
</body>
</html>
<?php $conn->close(); ?>