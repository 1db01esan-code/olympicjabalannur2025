<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}

$koneksi = new mysqli("localhost", "root", "", "turnamen_olimpiade");

$cabang_terpilih = $_GET['cabang'] ?? '';

// Ambil daftar peserta sesuai filter
if ($cabang_terpilih && $cabang_terpilih !== 'semua') {
  $stmt = $koneksi->prepare("SELECT * FROM pendaftaran WHERE cabang_olahraga = ?");
  $stmt->bind_param("s", $cabang_terpilih);
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  $result = $koneksi->query("SELECT * FROM pendaftaran");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Admin - Data Pendaftaran</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f2f2f2;
      padding: 30px;
    }

    h1 {
      text-align: center;
      color: #333;
    }

    form {
      text-align: center;
      margin-bottom: 20px;
    }

    select {
      padding: 8px;
      font-size: 16px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      background-color: #fff;
    }

    th, td {
      padding: 12px;
      border: 1px solid #ccc;
      text-align: center;
    }

    th {
      background-color: #007bff;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .back, .logout {
      display: inline-block;
      margin: 20px 10px;
      width: 200px;
      text-align: center;
      background-color: #007bff;
      color: white;
      padding: 10px;
      border-radius: 5px;
      text-decoration: none;
    }

    .back:hover, .logout:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>

  <h1>Data Pendaftaran Peserta Turnamen</h1>

  <form method="get">
    <label for="cabang">Filter Cabang Olahraga: </label>
    <select name="cabang" id="cabang" onchange="this.form.submit()">
      <option value="semua" <?= $cabang_terpilih === 'semua' ? 'selected' : '' ?>>Semua</option>
      <option value="Sepak Bola" <?= $cabang_terpilih === 'Sepak Bola' ? 'selected' : '' ?>>Sepak Bola</option>
      <option value="Bola Basket" <?= $cabang_terpilih === 'Bola Basket' ? 'selected' : '' ?>>Bola Basket</option>
      <option value="Catur" <?= $cabang_terpilih === 'Catur' ? 'selected' : '' ?>>Catur</option>
      <option value="Panahan" <?= $cabang_terpilih === 'Panahan' ? 'selected' : '' ?>>Panahan</option>
      <option value="Bola Voli" <?= $cabang_terpilih === 'Bola Voli' ? 'selected' : '' ?>>Bola Voli</option>
      <option value="E-SPORT" <?= $cabang_terpilih === 'E-SPORT' ? 'selected' : '' ?>>E-SPORT</option>
    </select>
  </form>

  <table>
    <tr>
      <th>No</th>
      <th>Nama Lengkap</th>
      <th>Nama Panggilan</th>
      <th>Umur</th>
      <th>Cabang Olahraga</th>
    </tr>
    <?php if ($result && $result->num_rows > 0): 
      $no = 1;
      while($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= $no++; ?></td>
      <td><?= htmlspecialchars($row["nama_lengkap"]); ?></td>
      <td><?= htmlspecialchars($row["nama_panggilan"]); ?></td>
      <td><?= htmlspecialchars($row["umur"]); ?></td>
      <td><?= htmlspecialchars($row["cabang_olahraga"]); ?></td>
    </tr>
    <?php endwhile; else: ?>
    <tr><td colspan="5">Tidak ada data peserta.</td></tr>
    <?php endif; ?>
  </table>

  <a class="back" href="http://localhost:8080/turnamen-olimpiade/index.html">← Kembali ke Menu Utama</a>
  <a class="logout" href="logout.php">Logout</a>

</body>
</html>

<?php $koneksi->close(); ?>