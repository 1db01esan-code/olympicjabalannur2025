<?php
// Koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "turnamen_olimpiade";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);
$conn->set_charset("utf8mb4");

// Ambil semua tim terdaftar
$tim = [];
$res = $conn->query("SELECT rw FROM peserta_volleyball ORDER BY rw ASC LIMIT 4");
while ($row = $res->fetch_assoc()) {
    $tim[] = $row['rw'];
}
$total_tim = count($tim);

// Jika tim = 4 → reset & generate jadwal
if ($total_tim == 4) {
    // Reset jadwal lama
    $conn->query("TRUNCATE TABLE jadwal_volleyball");

    // Mapping pendaftar 1–4
    $p1 = $tim[0];
    $p2 = $tim[1];
    $p3 = $tim[2];
    $p4 = $tim[3];

    // Jadwal fix sesuai permintaan
    $jadwal = [
        [
            'tanggal' => "2025-10-04",
            'pertandingan' => 1,
            'tim_a' => $p1,
            'tim_b' => $p2,
            'waktu' => "07.30 - 08.05"
        ],
        [
            'tanggal' => "2025-10-04",
            'pertandingan' => 2,
            'tim_a' => $p3,
            'tim_b' => $p4,
            'waktu' => "08.15 - 09.00"
        ],
        [
            'tanggal' => "2025-10-04",
            'pertandingan' => 3,
            'tim_a' => $p2,
            'tim_b' => $p3,
            'waktu' => "16.00 - 16.45"
        ],
        [
            'tanggal' => "2025-10-04",
            'pertandingan' => 4,
            'tim_a' => $p4,
            'tim_b' => $p1,
            'waktu' => "16.55 - 17.30"
        ],
        [
            'tanggal' => "2025-10-11",
            'pertandingan' => 5,
            'tim_a' => $p3,
            'tim_b' => $p1,
            'waktu' => "16.00 - 16.45"
        ],
        [
            'tanggal' => "2025-10-11",
            'pertandingan' => 6,
            'tim_a' => $p2,
            'tim_b' => $p4,
            'waktu' => "16.55 - 17.30"
        ],
    ];

    // Simpan ke database (skor otomatis NULL dari default DB)
    $stmt = $conn->prepare("INSERT INTO jadwal_volleyball (tanggal, pertandingan, tim_a, tim_b, waktu) VALUES (?,?,?,?,?)");
    foreach ($jadwal as $j) {
        $stmt->bind_param("sisss", $j['tanggal'], $j['pertandingan'], $j['tim_a'], $j['tim_b'], $j['waktu']);
        $stmt->execute();
    }
    $stmt->close();
}

// Ambil jadwal dari DB
$jadwal = [];
$resJ = $conn->query("SELECT * FROM jadwal_volleyball ORDER BY tanggal ASC, pertandingan ASC");
while ($row = $resJ->fetch_assoc()) {
    $jadwal[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bola Voli - Olympic Jabal An Nur 2025</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body { margin:0; font-family:'Inter', sans-serif; background:#f4f6f8; color:#333; }
.header { background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('images/volleyball.jpg') no-repeat center/cover; height: 300px; display:flex; flex-direction:column; align-items:center; justify-content:center; color:white; text-align:center; }
.header h1 { font-size:2.5rem; margin:0; font-weight:700; text-shadow:2px 2px 8px rgba(0,0,0,0.7); }
.register-btn { margin-top:15px; padding:12px 25px; background:#ff6b00; color:white; font-weight:600; text-decoration:none; border-radius:50px; transition:0.3s; }
.register-btn:hover { background:#e65c00; }
.container { max-width:1000px; margin: -50px auto 50px auto; background:white; padding:30px; border-radius:12px; box-shadow:0 8px 25px rgba(0,0,0,0.1); }
h2 { color:#ff6b00; margin-bottom:15px; font-size:1.8rem; }
ul { line-height:1.8; margin-left:20px; }
.contact { margin-top:30px; background:#fff3e0; padding:15px; border-left:6px solid #ff6b00; border-radius:8px; }
.contact a { color:#ff6b00; text-decoration:none; }
.contact a:hover { text-decoration:underline; }
a.back { display:block; text-align:center; margin-top:25px; text-decoration:none; color:#ff6b00; font-weight:600; }
a.back:hover { text-decoration:underline; }
table { width:100%; border-collapse:collapse; margin-top:20px; font-size:0.95rem; }
th, td { border:1px solid #ddd; padding:12px; text-align:center; }
th { background:#ff6b00; color:white; text-transform:uppercase; }
tr:nth-child(even) { background:#f9f9f9; }
.highlight { background:#ffe0b2; font-weight:700; text-align:center; font-size:1rem; }
.muted { color:#777; font-style:italic; }
@media(max-width:768px) { .header h1 { font-size:2rem; } table, th, td { font-size:0.8rem; } }
</style>
</head>
<body>

<div class="header">
  <h1>Bola Voli - Olympic Jabal An Nur 2025</h1>
  <a href="volleyballregister.php" class="register-btn">Daftar Sekarang</a>
</div>

<div class="container">
<h2>Aturan Pertandingan</h2>
<ul>
  <li>Waktu pertandingan 2 x 15 menit.</li>
  <li>Sistem poin sesuai hasil tiap ronde.</li>
  <li>Kuota tim bola voli dibatasi menjadi 4 tim. Setiap tim memiliki 9 pemain.</li>
  <li><strong>Lokasi pertandingan:</strong> Lapangan Voli Bukit Golf Arcadia 1/Blok A.</li>
  <li>Wasit pertandingan berasal dari tuan rumah.</li>
  <li><strong>Sistem Liga:</strong> Setiap tim saling bertemu sekali. Tim dengan poin tertinggi di klasemen akhir menjadi juara.</li>
  <li>Pendaftaran ditutup pada tanggal 26 September 2025, jam 19:00</li>
</ul>

<h2>Jadwal Pertandingan</h2>
<?php if ($total_tim < 4): ?>
    <p class="muted">Jadwal akan ditampilkan setelah 4 tim terdaftar.</p>
<?php else: ?>
    <table>
        <tr><th>Tanggal</th><th>Pertandingan</th><th>Tim 1</th><th>Tim 2</th><th>Waktu</th></tr>
        <?php foreach ($jadwal as $j): ?>
        <tr>
            <td><?= date("l, d M Y", strtotime($j['tanggal'])); ?></td>
            <td>Pertandingan <?= $j['pertandingan']; ?></td>
            <td>RW <?= htmlspecialchars($j['tim_a']); ?></td>
            <td>RW <?= htmlspecialchars($j['tim_b']); ?></td>
            <td><?= htmlspecialchars($j['waktu']); ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="highlight">
            <td colspan="5">🎉 Pembagian Hadiah: Sabtu, 11 Oktober 2025 pukul 17.30 - 18.00 🎉</td>
        </tr>
    </table>
<?php endif; ?>

<div class="contact">
    <strong>Contact Person:</strong><br>
    Pak Lilik - <a href="https://wa.me/628161300017" target="_blank">0816-1300-017 (Whatsapp)</a>
</div>

<a href="index.php" class="back">← Kembali ke Beranda</a>
</div>

</body>
</html>
<?php $conn->close(); ?>