<?php
session_start();
$conn = new mysqli("localhost", "root", "", "turnamen_olimpiade");

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cek apakah email ada di database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Login berhasil, simpan data ke session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['email'] = $user['email'];

                echo "<script>alert('Login berhasil!'); window.location.href = 'index.php';</script>";
                exit;
            } else {
                echo "<script>alert('Password salah!'); window.location.href = 'login.php';</script>";
            }
        } else {
            echo "<script>alert('Email tidak ditemukan!'); window.location.href = 'login.php';</script>";
        }
    } else {
        echo "<script>alert('Terjadi kesalahan.'); window.location.href = 'login.php';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
