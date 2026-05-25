<?php
session_start();
require_once "koneksi.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';

    // Cek apakah field kosong
    if (empty($email) || empty($password)) {
        $error = "Email dan password wajib diisi.";
    } else {
        // Ambil data user dari database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Cek apakah user ditemukan
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Verifikasi password
            if (password_verify($password, $user["password"])) {
                $_SESSION["user_logged_in"] = true;
                $_SESSION["user_email"] = $user["email"];
                $_SESSION["user_nama"] = $user["nama"];
                header("Location: index.php");
                exit;
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Email tidak ditemukan!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login User - Olympic Jabal An Nur</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #000046, #1CB5E0);
      color: white;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-box {
      background-color: rgba(0,0,0,0.8);
      padding: 40px;
      border-radius: 12px;
      width: 350px;
      text-align: center;
    }

    h2 {
      margin-bottom: 20px;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border: none;
      border-radius: 8px;
    }

    input[type="submit"] {
      padding: 10px 20px;
      background-color: #28a745;
      border: none;
      color: white;
      border-radius: 8px;
      cursor: pointer;
    }

    .error {
      background-color: #ff4d4d;
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 10px;
    }

    .link-register {
      margin-top: 15px;
      display: block;
      color: #ccc;
    }

    .link-register:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Login User</h2>
    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <input type="email" name="email" placeholder="Email" required><br>
      <input type="password" name="password" placeholder="Password" required><br>
      <input type="submit" value="Login">
    </form>
    <a class="link-register" href="register.php">Belum punya akun? Daftar di sini</a>
  </div>
</body>
</html>
