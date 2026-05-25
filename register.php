<?php
require_once "koneksi.php";
$error = "";
$sukses = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nama = $_POST["nama_lengkap"] ?? '';
  $email = $_POST["email"] ?? '';
  $password = $_POST["password"] ?? '';

  // Validasi kosong
  if (empty($nama) || empty($email) || empty($password)) {
    $error = "Semua field wajib diisi.";
  } else {
    // Cek apakah email sudah ada
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
      $error = "Email sudah terdaftar!";
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $insert = $conn->prepare("INSERT INTO users (nama_lengkap, email, password) VALUES (?, ?, ?)");
      $insert->bind_param("sss", $nama, $email, $hash);
      if ($insert->execute()) {
        $sukses = "Registrasi berhasil! Silakan login.";
      } else {
        $error = "Gagal menyimpan data.";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Register User - Olympic Jabal An Nur</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #1c92d2, #f2fcfe);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .register-box {
      background-color: rgba(255,255,255,0.95);
      padding: 40px;
      border-radius: 12px;
      width: 350px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.2);
    }

    h2 {
      margin-bottom: 20px;
      color: #333;
      text-align: center;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    input[type="submit"] {
      background-color: #007bff;
      border: none;
      padding: 10px 20px;
      color: white;
      border-radius: 6px;
      cursor: pointer;
      width: 100%;
    }

    .error, .sukses {
      background-color: #ffdddd;
      padding: 10px;
      border-radius: 6px;
      color: #a94442;
      margin-bottom: 15px;
      text-align: center;
    }

    .sukses {
      background-color: #dff0d8;
      color: #3c763d;
    }

    .login-link {
      text-align: center;
      display: block;
      margin-top: 15px;
      color: #555;
    }

    .login-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="register-box">
    <h2>Daftar Akun Baru</h2>

    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($sukses)): ?>
      <div class="sukses"><?= htmlspecialchars($sukses) ?></div>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
      <input type="email" name="email" placeholder="Email Aktif" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="submit" value="Daftar">
    </form>

    <a class="login-link" href="login.php">Sudah punya akun? Login di sini</a>
  </div>
</body>
</html>
