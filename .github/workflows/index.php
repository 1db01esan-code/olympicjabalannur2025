<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Olympic Jabal An Nur 2025</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }

    body {
      background: url('img/bg-turnamen.jpg') no-repeat center center fixed;
      background-size: cover;
      color: white;
      position: relative;
    }

    header {
      background-color: rgba(0, 74, 173, 0.9);
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header h1 {
      font-size: 24px;
      color: white;
    }

    .login-button {
      background-color: #007bff;
      color: white;
      padding: 8px 16px;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
      margin-left: 10px;
    }

    .logout-button {
      background-color: #dc3545;
      color: white;
      padding: 8px 16px;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
      margin-left: 10px;
    }

    .content {
      padding: 60px 20px 40px;
      text-align: center;
      text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.8);
    }

    .content h2 {
      font-size: 28px;
      margin-bottom: 15px;
    }

    .content p {
      max-width: 700px;
      margin: 0 auto 40px;
      font-size: 16px;
      line-height: 1.6;
      background-color: rgba(0, 0, 0, 0.5);
      padding: 20px;
      border-radius: 10px;
    }

    .menu {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
    }

    .menu-item {
      background-color: rgba(0, 0, 0, 0.5);
      color: white;
      text-align: center;
      padding: 16px;
      border-radius: 12px;
      width: 140px;
      text-decoration: none;
      transition: transform 0.3s;
      box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }

    .menu-item:hover {
      transform: translateY(-5px);
      background-color: rgba(0, 0, 0, 0.7);
    }

    .menu-item img {
      width: 40px;
      height: 40px;
      margin-bottom: 8px;
    }

    footer {
      background-color: rgba(0, 0, 0, 0.6);
      padding: 15px;
      text-align: center;
      font-size: 14px;
      color: #eee;
      margin-top: 40px;
    }
  </style>
</head>
<body>

<header>
  <h1>OLYMPIC JABAL AN NUR 2025</h1>
  <div>
    <a class="login-button" href="admin_login.php">Login Admin (Khusus Panitia)</a>
  </div>
</header>

<div class="content">
  <h2>Turnamen Silaturahmi Warga Bukit Golf</h2>
  <p>
    Olympic Jabal An Nur 2025 merupakan ajang olahraga tahunan yang diselenggarakan oleh Masjid Jabal An Nur
    untuk mempererat hubungan antarwarga Bukit Golf. Turnamen ini tidak hanya menjadi ajang kompetisi, tetapi
    juga menjadi wadah menjaga persaudaraan dan menjalin silaturahmi antar sesama manusia tanpa memandang
    latar belakang agama. Semua warga, tanpa terkecuali, dipersilakan ikut serta dan meramaikan suasana dengan
    semangat sportivitas dan kebersamaan.
  </p>

  <div class="menu">
    <!-- Menu Cabang Olahraga -->
    <a href="soccer.php" class="menu-item">
      <img src="img/soccer.png" alt="Minisoccer">
      <div>Minisoccer</div>
    </a>
    <a href="basketball.php" class="menu-item">
      <img src="img/basketball.png" alt="Bola Basket">
      <div>Bola Basket</div>
    </a>
    <a href="chess.php" class="menu-item">
      <img src="img/chess.png" alt="Catur">
      <div>Catur</div>
    </a>
    <a href="archery.php" class="menu-item">
      <img src="img/archery.png" alt="Panahan">
      <div>Panahan</div>
    </a>
    <a href="volleyball.php" class="menu-item">
      <img src="img/volleyball.png" alt="Bola Voli">
      <div>Bola Voli</div>
    </a>

    <!-- Menu Jadwal -->
    <a href="jadwal.php" class="menu-item">
      <img src="img/schedule.png" alt="Jadwal">
      <div>Jadwal</div>
    </a>


    <!-- Menu Lainnya -->
    <a href="galeri.php" class="menu-item">
      <img src="img/gallery.png" alt="Galeri">
      <div>Galeri</div>
    </a>
    <a href="sejarahmasjid.php" class="menu-item">
      <img src="img/masjid.png" alt="Sejarah Masjid">
      <div>Sejarah Masjid</div>
    </a>
    <a href="result.php" class="menu-item">
      <img src="img/result.png" alt="Hasil Pertandingan">
      <div>Hasil Pertandingan</div>
    </a>
    <a href="trophy.php" class="menu-item">
      <img src="img/trophy.png" alt="Pengumuman Juara">
      <div>Pengumuman Juara</div>
    </a>
  </div>
</div>

<footer>
  &copy; 2025 Olympic Jabal An Nur. All rights reserved.
</footer>

</body>
</html>