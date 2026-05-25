<?php
// PHP tetap sama seperti versi sebelumnya
$host = "localhost";
$user = "root";
$pass = "";
$db   = "turnamen_olimpiade";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = $conn->query("SELECT COUNT(*) as total FROM peserta_archery");
    $row = $result->fetch_assoc();
    $total = $row['total'];

    if ($total >= 4) {
        die("<h2 style='color:red; text-align:center;'>Kuota peserta sudah penuh (4 orang)</h2>");
    }

    $nama = $_POST['nama_peserta'];
    $rw   = $_POST['rw'];
    $pic  = $_POST['nama_pic'];
    $telp = $_POST['no_telp_pic'];

    $stmt = $conn->prepare("INSERT INTO peserta_archery (nama_peserta, rw, nama_pic, no_telp_pic, waktu_pendaftaran) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $nama, $rw, $pic, $telp);

    if ($stmt->execute()) {
        echo "<!DOCTYPE html>
        <html lang='id'>
        <head>
          <meta charset='UTF-8'>
          <title>Pendaftaran Panahan</title>
          <link href='https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap' rel='stylesheet'>
          <style>
            body { font-family: 'Inter', sans-serif; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; background:#f0f4f8; }
            .card { background:white; padding:40px 30px; border-radius:15px; box-shadow:0 10px 25px rgba(0,0,0,0.1); text-align:center; max-width:450px; width:90%; }
            h2 { color:#10b981; margin-bottom:15px; }
            p { color:#374151; }
            a { display:inline-block; margin-top:25px; padding:10px 25px; background:#3b82f6; color:white; text-decoration:none; border-radius:8px; font-weight:600; transition:0.3s; }
            a:hover { background:#2563eb; }
          </style>
        </head>
        <body>
          <div class='card'>
            <h2>Selamat, Anda berhasil mendaftar!</h2>
            <p>Terima kasih telah mendaftar di cabang Panahan Olympic Jabal An Nur 2024.</p>
            <p>Anda bermain pada tanggal 3 Oktober 2025.</p>
            <a href='archery.php'>&larr; Kembali ke Halaman Panahan</a>
          </div>
        </body>
        </html>";
    } else {
        echo "Terjadi kesalahan: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Form Pendaftaran Panahan</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; background:#f0f4f8; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }
    .form-container { background:white; padding:40px 30px; border-radius:15px; box-shadow:0 10px 25px rgba(0,0,0,0.1); width:100%; max-width:500px; }
    h2 { text-align:center; color:#3b82f6; margin-bottom:25px; }
    label { display:block; margin-top:15px; font-weight:600; color:#374151; }
    input { width:100%; padding:12px 15px; margin-top:5px; border:1px solid #d1d5db; border-radius:8px; transition:0.3s; }
    input:focus { border-color:#3b82f6; outline:none; box-shadow:0 0 0 3px rgba(59,130,246,0.2); }
    button { width:100%; margin-top:25px; padding:12px; background:#10b981; color:white; border:none; border-radius:10px; font-weight:600; cursor:pointer; transition:0.3s; }
    button:hover { background:#059669; }
    a { display:block; text-align:center; margin-top:20px; color:#3b82f6; text-decoration:none; font-weight:500; }
    a:hover { text-decoration:underline; }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Pendaftaran Panahan</h2>
    <form method="POST" action="">
      <label>Nama Peserta</label>
      <input type="text" name="nama_peserta" required>

      <label>RW</label>
      <input type="text" name="rw" required>

      <label>Nama PIC</label>
      <input type="text" name="nama_pic" required>

      <label>No Telp PIC</label>
      <input type="text" name="no_telp_pic" required>

      <button type="submit">Daftar</button>
    </form>
    <a href="archery.php">&larr; Kembali ke Halaman Panahan</a>
  </div>
</body>
</html>