<?php
// Koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "turnamen_olimpiade";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Ambil hasil pertandingan
$hasil = [];
$res = $conn->query("SELECT * FROM hasil_volleyball ORDER BY id ASC");
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
  <title>Hasil Pertandingan Bola Voli</title>
  <style>
    body { margin:0; font-family: Arial, sans-serif; background:#f4f6f9; color:#333; }
    .container {
      max-width: 900px; margin:40px auto; background:#fff; padding:20px;
      border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1);
    }
    h1 { text-align:center; color:#007bff; }
    table {
      width:100%; border-collapse: collapse; margin-top:20px;
    }
    th, td {
      border:1px solid #ccc; padding:10px; text-align:center;
    }
    th { background:#007bff; color:white; }
    tr:nth-child(even) { background:#f9f9f9; }
    .status-selesai { color:green; font-weight:bold; }
    .status-belum { color:red; font-weight:bold; }
    a.back {
      display:block; margin-top:20px; text-align:center;
      text-decoration:none; color:#007bff;
    }
    a.back:hover { text-decoration:underline; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Hasil Pertandingan Bola Voli</h1>

    <?php if (empty($hasil)): ?>
      <p style="text-align:center; color:#777;">Belum ada hasil pertandingan yang tercatat.</p>
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
            <td class="<?= $h['status'] === 'Selesai' ? 'status-selesai':'status-belum' ?>">
              <?= htmlspecialchars($h['status']) ?>
            </td>
            <td><?= htmlspecialchars($h['updated_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>

    <a href="index.html" class="back">← Kembali ke Beranda</a>
  </div>
</body>
</html>
<?php $conn->close(); ?>