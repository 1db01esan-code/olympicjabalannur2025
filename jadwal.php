<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "turnamen_olimpiade";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }
$conn->set_charset("utf8mb4");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Jadwal Pertandingan - Olympic Jabal An Nur 2025</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    body { font-family: Arial, sans-serif; margin:0; background:#f9f9f9; }
    header { background:#004aad; color:white; padding:20px; }
    header h1 { margin:0; }
    section { padding:30px; }
    table { width:100%; border-collapse:collapse; margin:20px 0; background:white; }
    table th, table td { border:1px solid #ddd; padding:10px; text-align:center; }
    table th { background:#004aad; color:white; }
    h2 { margin-top:40px; color:#004aad; }
    footer { background:#004aad; color:white; padding:15px; text-align:center; margin-top:40px; }
  </style>
</head>
<body>
  <header>
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <h1>📅 Jadwal Pertandingan</h1>
      <a href="index.html" style="color:white; text-decoration:none; font-weight:bold;">🏠 Kembali ke Beranda</a>
    </div>
  </header>

  <section>
    <h2>⚽ Sepak Bola</h2>
    <table>
      <tr><th>Pertandingan</th><th>Tanggal</th><th>Waktu</th><th>Tim 1</th><th>Tim 2</th></tr>
      <?php
      $res=$conn->query("SELECT * FROM jadwal_soccer");
      while($row=$res->fetch_assoc()){
        echo "<tr>
          <td>{$row['pertandingan']}</td>
          <td>{$row['tanggal']}</td>
          <td>{$row['waktu']}</td>
          <td>{$row['tim1_rw']}</td>
          <td>{$row['tim2_rw']}</td>
        </tr>";
      }
      ?>
    </table>

    <h2>🏀 Bola Basket</h2>
    <table>
      <tr><th>Pertandingan</th><th>Ronde</th><th>Tanggal</th><th>Waktu</th><th>Tim A</th><th>Tim B</th></tr>
      <?php
      $res=$conn->query("SELECT * FROM jadwal_basket");
      while($row=$res->fetch_assoc()){
        echo "<tr>
          <td>{$row['pertandingan']}</td>
          <td>{$row['ronde']}</td>
          <td>{$row['tanggal']}</td>
          <td>{$row['waktu']}</td>
          <td>{$row['tim_a']}</td>
          <td>{$row['tim_b']}</td>
        </tr>";
      }
      ?>
    </table>

    <h2>♟️ Catur</h2>
    <table>
      <tr><th>Ronde</th><th>Meja</th><th>Pemain 1</th><th>Pemain 2</th><th>Waktu</th></tr>
      <?php
      $res=$conn->query("SELECT * FROM jadwal_chess");
      while($row=$res->fetch_assoc()){
        echo "<tr>
          <td>{$row['ronde']}</td>
          <td>{$row['meja']}</td>
          <td>{$row['pemain1']}</td>
          <td>{$row['pemain2']}</td>
          <td>{$row['waktu']}</td>
        </tr>";
      }
      ?>
    </table>

    <h2>🏹 Panahan</h2>
    <table>
      <tr><th>Pertandingan</th><th>Nama</th><th>Peserta 1</th><th>Peserta 2</th><th>Peserta 3</th><th>Peserta 4</th><th>Waktu</th></tr>
      <?php
      $res=$conn->query("SELECT * FROM jadwal_archery");
      while($row=$res->fetch_assoc()){
        echo "<tr>
          <td>{$row['pertandingan']}</td>
          <td>{$row['nama_pertandingan']}</td>
          <td>{$row['peserta1']}</td>
          <td>{$row['peserta2']}</td>
          <td>{$row['peserta3']}</td>
          <td>{$row['peserta4']}</td>
          <td>{$row['waktu']}</td>
        </tr>";
      }
      ?>
    </table>

    <h2>🏐 Bola Voli</h2>
    <table>
      <tr><th>Pertandingan</th><th>Tanggal</th><th>Waktu</th><th>Tim A</th><th>Tim B</th></tr>
      <?php
      $res=$conn->query("SELECT * FROM jadwal_volleyball");
      while($row=$res->fetch_assoc()){
        echo "<tr>
          <td>{$row['pertandingan']}</td>
          <td>{$row['tanggal']}</td>
          <td>{$row['waktu']}</td>
          <td>{$row['tim_a']}</td>
          <td>{$row['tim_b']}</td>
        </tr>";
      }
      ?>
    </table>

    <h3 style="margin-top:40px;">🎉 Grand Prize & Penutupan</h3>
    <p style="font-size:18px; line-height:1.8;">
      🏆 <strong>Pembagian Hadiah</strong> — 11 Oktober 2025
    </p>
  </section>

  <footer>
    &copy; 2025 Jabal An Nur Olympic Committee
  </footer>
</body>
</html>
<?php $conn->close(); ?>