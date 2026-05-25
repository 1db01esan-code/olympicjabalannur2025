<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $nama_panggilan = $_POST['nama_panggilan'];
    $umur = $_POST['umur'];
    $olahraga = $_POST['olahraga'];

    $query = "UPDATE pendaftaran SET 
        nama_lengkap = '$nama_lengkap',
        nama_panggilan = '$nama_panggilan',
        umur = '$umur',
        olahraga = '$olahraga'
        WHERE id = '$id'";

    if (mysqli_query($conn, $query)) {
        header("Location: daftar_peserta.php?pesan=update_sukses");
    } else {
        echo "Gagal mengupdate data: " . mysqli_error($conn);
    }
}
?>