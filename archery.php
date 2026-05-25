<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "turnamen_olimpiade";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);
$conn->set_charset("utf8mb4");

// Ambil semua peserta panahan, urut RW
$peserta = [];
$resPeserta = $conn->query("SELECT * FROM peserta_archery ORDER BY rw ASC");
while ($row = $resPeserta->fetch_assoc()) {
    $peserta[] = $row;
}
$totalPeserta = count($peserta);

// --- Fungsi bantu: cek apakah jadwal sudah ada ---
function jadwalSudahAda(mysqli $conn): bool {
    $res = $conn->query("SELECT COUNT(*) AS jml FROM jadwal_archery");
    $row = $res ? $res->fetch_assoc() : ['jml' => 0];
    return ((int)$row['jml']) > 0;
}

// --- Hapus jadwal lama jika peserta kurang dari 4 ---
if ($totalPeserta < 4) {
    $conn->query("TRUNCATE TABLE jadwal_archery");
}

// --- Otomatisasi jadwal jika peserta >= 4 ---
if ($totalPeserta >= 4 && !jadwalSudahAda($conn)) {

    // Hapus jadwal lama dulu (jika ada)
    $conn->query("TRUNCATE TABLE jadwal_archery");

    $stmt = $conn->prepare("INSERT INTO jadwal_archery
        (nama_pertandingan, peserta1, peserta2, peserta3, peserta4, skor1, skor2, skor3, skor4, waktu)
        VALUES (?,?,?,?,?,?,?,?,?,?)");

    $namaPertandingan = "Pertandingan Bersama";
    $waktu = "07.30 - 07.45";

    // Ambil 4 peserta pertama atau string kosong jika kurang
    $p = [];
    for ($i = 0; $i < 4; $i++) {
        $p[$i] = isset($peserta[$i]) ? $peserta[$i]['nama_peserta'] : "";
    }

    $s1 = $s2 = $s3 = $s4 = 0;

    $stmt->bind_param("ssssssssis",
        $namaPertandingan,
        $p[0], $p[1], $p[2], $p[3],
        $s1, $s2, $s3, $s4, $waktu
    );
    $stmt->execute();
    $stmt->close();
}

// --- Ambil jadwal untuk ditampilkan ---
$jadwal = [];
$resJ = $conn->query("SELECT * FROM jadwal_archery ORDER BY pertandingan ASC");
while ($row = $resJ->fetch_assoc()) {
    $jadwal[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Panahan - Olympic Jabal An Nur 2025</title>
<style>
body { margin:0; font-family:Arial,sans-serif; background:#f2f2f2; color:#333; }
.header { background:url('images/archery.jpg') no-repeat center/cover; height:340px; display:flex; flex-direction:column; align-items:center; justify-content:center; color:white; text-shadow:2px 2px 4px #000; }
.header h1 { font-size:40px; margin:0; }
.register-btn { margin-top:20px; padding:10px 20px; background-color:#28a745; color:white; border-radius:6px; text-decoration:none; font-weight:bold; box-shadow:0 4px 8px rgba(0,0,0,0.2); transition:background 0.3s ease; }
.register-btn:hover { background-color:#218838; }
.container { max-width:800px; margin:30px auto; background:white; padding:20px; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
h2 { color:#007bff; }
ul { line-height:1.7; }
.contact { margin-top:30px; background:#e0f7fa; padding:15px; border-left:5px solid #007bff; border-radius:8px; }
.contact strong { color:#007bff; }
a.back { display:block; text-align:center; margin-top:20px; text-decoration:none; color:#007bff; }
a.back:hover { text-decoration:underline; }
table { width:100%; border-collapse:collapse; margin-top:15px; }
th, td { border:1px solid #ccc; padding:8px; text-align:center; }
th { background:#f2f2f2; }
.badge-wait { display:inline-block; padding:2px 8px; border-radius:8px; background:#fff3cd; color:#8a6d3b; font-size:12px; }
</style>
</head>
<body>

<div class="header">
  <h1>Panahan - Olympic Jabal An Nur 2025</h1>
  <a href="archeryregister.php" class="register-btn">Daftar Sekarang</a>
</div>

<div class="container">
<h2>Aturan Pertandingan</h2>
<ul>
  <li>Sistem pertandingan: Semua peserta bermain bersamaan</li>
  <li>Setiap peserta menembakkan anak panah 3 kali</li>
  <li>Tepat di tengah target = 5 poin, skor maksimal = 15</li>
  <li>Hasil seri: shoot-off 1 anak panah</li>
  <li>Perlengkapan keselamatan wajib dipakai</li>
  <li>Keputusan juri bersifat mutlak</li>
  <li><strong>Lokasi:</strong> Halaman Masjid Jami Jabal An Nur</li>
  <li>Pendaftaran ditutup: 26 September 2025, jam 19:00</li>
  <li><strong>Pertandingan dimulai: Sabtu, 4 Oktober 2025</strong></li>
</ul>

<h2>Jadwal Pertandingan</h2>
<?php if ($totalPeserta < 4): ?>
  <p style="color:red; font-weight:bold;">Jadwal akan dibuat otomatis setelah 4 peserta terdaftar.</p>
<?php else: ?>
  <table>
    <tr>
      <th>Pertandingan</th>
      <th>Peserta 1</th>
      <th>Peserta 2</th>
      <th>Peserta 3</th>
      <th>Peserta 4</th>
      <th>Waktu</th>
    </tr>
    <?php foreach ($jadwal as $row): ?>
      <tr>
        <td><?= htmlspecialchars($row['nama_pertandingan']); ?></td>
        <td><?= htmlspecialchars($row['peserta1']) ?: '<span class="badge-wait">Menunggu</span>'; ?></td>
        <td><?= htmlspecialchars($row['peserta2']) ?: '<span class="badge-wait">Menunggu</span>'; ?></td>
        <td><?= htmlspecialchars($row['peserta3']) ?: '<span class="badge-wait">Menunggu</span>'; ?></td>
        <td><?= htmlspecialchars($row['peserta4']) ?: '<span class="badge-wait">Menunggu</span>'; ?></td>
        <td><?= htmlspecialchars($row['waktu']); ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>

<div class="contact">
  <strong>Contact Person:</strong><br>
  Pak Bobby - <a href="https://wa.me/6282231485613" target="_blank">0822-3148-5613 (Whatsapp)</a>
</div>

<a href="index.php" class="back">&larr; Kembali ke Beranda</a>
</div>

</body>
</html>