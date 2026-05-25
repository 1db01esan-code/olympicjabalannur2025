<?php
// Debug sementara
error_reporting(E_ALL);
ini_set('display_errors', 1);

// koneksi DB
$host = "localhost";
$user = "root";
$pass = "";
$db   = "turnamen_olimpiade";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rw      = $_POST['rw'];
    $pemain1 = $_POST['pemain1'];
    $pemain2 = $_POST['pemain2'];
    $pemain3 = $_POST['pemain3'];
    $pemain4 = $_POST['pemain4'];
    $pemain5 = $_POST['pemain5'];
    $pic     = $_POST['pic'];
    $telepon = $_POST['telepon'];

    $sql = "INSERT INTO peserta_basket 
            (rw, pemain1, pemain2, pemain3, pemain4, pemain5, pic, telepon, status) 
            VALUES 
            ('$rw', '$pemain1', '$pemain2', '$pemain3', '$pemain4', '$pemain5', '$pic', '$telepon', 'Terdaftar')";

    if ($conn->query($sql) === TRUE) {
        echo "<!DOCTYPE html>
        <html lang='id'>
        <head>
          <meta charset='UTF-8'>
          <title>Pendaftaran Basket Sukses</title>
          <style>
            body { font-family:'Segoe UI', Tahoma, sans-serif; background:#f4f9f4; padding:40px; }
            .card { background:#fff; padding:30px; border-radius:15px; max-width:700px; margin:auto;
                    box-shadow:0 6px 18px rgba(0,0,0,0.1); text-align:center; }
            h2 { color:#28a745; }
            ul { text-align:left; display:inline-block; margin:20px auto; padding:0; list-style:none; }
            ul li { background:#f0f0f0; margin:4px 0; padding:8px 12px; border-radius:6px; }
            a.btn { display:inline-block; margin-top:20px; padding:12px 20px; background:#dc2430;
                    color:#fff; text-decoration:none; border-radius:8px; font-weight:bold; }
            a.btn:hover { background:#b71c1c; }
          </style>
        </head>
        <body>
          <div class='card'>
            <h2>✅ RW {$rw} berhasil terdaftar!</h2>
            <p><b>PIC:</b> {$pic} ({$telepon})</p>
            <h3>Daftar Pemain:</h3>
            <ul>
              <li>" . htmlspecialchars($pemain1) . "</li>
              <li>" . htmlspecialchars($pemain2) . "</li>
              <li>" . htmlspecialchars($pemain3) . "</li>
              <li>" . htmlspecialchars($pemain4) . "</li>
              <li>" . htmlspecialchars($pemain5) . "</li>
            </ul>
            <p style='margin-top:20px;font-size:16px;color:#333;'>
              🏀 Anda akan bermain pada tanggal <b>6, 13 & 14 September 2025</b>.<br>
              Silahkan membaca informasi detail di halaman utama.
            </p>
            <a href='basketball.php' class='btn'>&larr; Kembali ke Halaman Basket</a>
          </div>
        </body>
        </html>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pendaftaran Bola Basket</title>
  <style>
    body { font-family:'Segoe UI', Tahoma, sans-serif; background:#eef2f7; margin:0; padding:40px; }
    .container { max-width:700px; margin:auto; background:#fff; padding:30px; border-radius:15px; box-shadow:0 6px 18px rgba(0,0,0,0.1); }
    h1 { text-align:center; margin-bottom:20px; color:#2c3e50; }
    label { display:block; margin-top:12px; font-weight:bold; color:#34495e; }
    input { width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:8px; }
    button { margin-top:20px; padding:14px; width:100%; background:#dc2430; color:#fff; border:none; border-radius:8px; font-weight:bold; cursor:pointer; font-size:16px; }
    button:hover { background:#b71c1c; }
  </style>
</head>
<body>

  <div class="container">
    <h1>Formulir Pendaftaran - Bola Basket</h1>
    <form method="POST" action="">
      <label>RW</label>
      <input type="number" name="rw" required>

      <label>Nama Pemain 1</label>
      <input type="text" name="pemain1" required>

      <label>Nama Pemain 2</label>
      <input type="text" name="pemain2" required>

      <label>Nama Pemain 3</label>
      <input type="text" name="pemain3" required>

      <label>Nama Pemain 4</label>
      <input type="text" name="pemain4" required>

      <label>Nama Pemain 5</label>
      <input type="text" name="pemain5" required>

      <label>Nama PIC</label>
      <input type="text" name="pic" required>

      <label>No Telp PIC</label>
      <input type="text" name="telepon" required>

      <button type="submit">Daftar</button>
    </form>
  </div>

</body>
</html>