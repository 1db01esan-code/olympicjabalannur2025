<?php
// galeri.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Galeri Dokumentasi - Olympic Jabal An Nur 2025</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f7fa;
      color: #333;
    }

    header {
      background-color: #004aad;
      color: white;
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header h1 {
      margin: 0;
      font-size: 24px;
    }

    .back-link a {
      color: white;
      text-decoration: none;
      background-color: #007bff;
      padding: 8px 16px;
      border-radius: 6px;
      font-weight: bold;
      transition: background-color 0.3s;
    }

    .back-link a:hover {
      background-color: #0056b3;
    }

    .container {
      max-width: 1000px;
      margin: 40px auto;
      padding: 0 20px;
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #004aad;
    }

    .gallery {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
    }

    .gallery img {
      width: 100%;
      height: auto;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }

    .gallery img:hover {
      transform: scale(1.05);
    }

    footer {
      text-align: center;
      padding: 20px;
      font-size: 14px;
      color: #777;
      background-color: #eee;
      margin-top: 40px;
    }
  </style>
</head>
<body>

  <header>
    <h1>Galeri Dokumentasi</h1>
    <div class="back-link">
      <a href="index.php">Kembali ke Beranda</a>
    </div>
  </header>

  <div class="container">
    <h2>Dokumentasi Olympic Jabal An Nur 2025</h2>
    <div class="gallery">
      <img src="img/galeri1.jpg" alt="Dokumentasi 1">
      <img src="img/galeri2.jpg" alt="Dokumentasi 2">
      <img src="img/galeri3.jpg" alt="Dokumentasi 3">
      <img src="img/galeri4.jpg" alt="Dokumentasi 4">
      <img src="img/galeri5.jpg" alt="Dokumentasi 5">
      <img src="img/galeri6.jpg" alt="Dokumentasi 6">
      <img src="img/galeri7.jpg" alt="Dokumentasi 7">
      <img src="img/galeri8.jpg" alt="Dokumentasi 8">
    </div>
  </div>

  <footer>
    &copy; 2025 Olympic Jabal An Nur. Dokumentasi oleh Panitia & Relawan.
  </footer>

</body>
</html>