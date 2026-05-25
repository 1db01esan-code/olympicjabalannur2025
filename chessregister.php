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

$message = "";

// Proses form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_peserta = $_POST['nama_peserta'] ?? '';
    $rw           = $_POST['rw'] ?? '';
    $pic          = $_POST['pic'] ?? '';
    $telp_pic     = $_POST['telp_pic'] ?? '';

    // Cek kuota
    $result = $conn->query("SELECT COUNT(*) as total FROM peserta_chess");
    $row = $result->fetch_assoc();
    $total = $row['total'];

    if ($total >= 8) {
        $message = "<p style='color:red;'>Kuota sudah penuh (8 peserta). Pendaftaran ditutup.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO peserta_chess 
            (nama_peserta, rw, pic, telp_pic) 
            VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama_peserta, $rw, $pic, $telp_pic);

        if ($stmt->execute()) {
            $message = "<p style='color:green;'>
                Selamat anda terdaftar sebagai peserta catur.<br>
                Anda akan bermain pada tanggal <strong>27 September 2025</strong>.
            </p>";
        } else {
            $message = "<p style='color:red;'>Terjadi kesalahan: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pendaftaran Catur</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(to right, #e0f7fa, #fff);
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 600px;
      margin: 50px auto;
      background: white;
      padding: 40px 30px;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }
    .container:hover {
      transform: translateY(-5px);
    }
    h1 {
      text-align: center;
      color: #007bff;
      margin-bottom: 30px;
      font-weight: 700;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    label {
      font-weight: 600;
      margin-bottom: 5px;
      color: #333;
    }
    input {
      padding: 12px 15px;
      border: 1px solid #ccc;
      border-radius: 10px;
      font-size: 16px;
      transition: border 0.3s ease, box-shadow 0.3s ease;
    }
    input:focus {
      border-color: #007bff;
      box-shadow: 0 0 8px rgba(0,123,255,0.2);
      outline: none;
    }
    button {
      padding: 15px;
      background: #007bff;
      color: white;
      font-weight: 600;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s ease;
    }
    button:hover {
      background: #0056b3;
    }
    .msg {
      text-align: center;
      margin-top: 20px;
      font-size: 16px;
    }
    a.back {
      display: block;
      margin-top: 25px;
      text-align: center;
      color: #007bff;
      text-decoration: none;
      font-weight: 600;
    }
    a.back:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="container">
  <h1>Formulir Pendaftaran Catur</h1>

  <?php if ($message) echo "<div class='msg'>$message</div>"; ?>

  <form method="POST" action="">
    <label for="nama_peserta">Nama Peserta:</label>
    <input type="text" id="nama_peserta" name="nama_peserta" required>

    <label for="rw">RW:</label>
    <input type="text" id="rw" name="rw" required>

    <label for="pic">Nama Penanggung Jawab (PIC):</label>
    <input type="text" id="pic" name="pic" required>

    <label for="telp_pic">No. Telepon PIC:</label>
    <input type="text" id="telp_pic" name="telp_pic" required>

    <button type="submit">Daftar</button>
  </form>

  <a href="chess.php" class="back">← Kembali ke Halaman Catur</a>
</div>

</body>
</html>