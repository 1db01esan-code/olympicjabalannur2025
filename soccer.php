<?php
// soccer.php
// Koneksi DB
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'turnamen_olimpiade';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Ambil 4 pendaftar pertama berdasarkan RW terkecil
$sql = "SELECT rw, nama_pic FROM peserta_soccer ORDER BY rw ASC LIMIT 4";
$result = $conn->query($sql);

$teams = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Bila rw kosong di DB, kita jadikan null supaya pengecekan lebih konsisten
        $teams[] = [
            'rw'  => ($row['rw'] === null || $row['rw'] === '') ? null : (int)$row['rw'],
            'pic' => $row['nama_pic']
        ];
    }
}

// Lengkapi jadi 4 slot
for ($i = count($teams); $i < 4; $i++) {
    $teams[] = ['rw' => null, 'pic' => '-'];
}

// Hitung jumlah tim valid
$jumlahTim = 0;
foreach ($teams as $t) {
    if ($t['rw'] !== null) $jumlahTim++;
}

// Helper tampilan tim
function labelTim($t) {
    if ($t['rw'] === null) return 'Belum Terdaftar';
    return 'RW ' . $t['rw'];
}

// --- Hapus jadwal lama hanya jika sudah 4 tim ---
if ($jumlahTim === 4) {
    $conn->query("DELETE FROM jadwal_soccer");
}

// Susun jadwal otomatis jika ada 4 tim
$jadwal = [];
if ($jumlahTim === 4) {
    $p1 = $teams[0];
    $p2 = $teams[1];
    $p3 = $teams[2];
    $p4 = $teams[3];

    $jadwal = [
        [
            'tanggal' => '2025-09-06',
            'label'   => 'Sabtu, 6 September 2025',
            'rentang' => '16.00 - 17.00',
            'pertandingan' => [
                ['no' => 1, 'waktu' => '16.00 - 16.25', 'tim1' => $p1, 'tim2' => $p2],
                ['no' => 2, 'waktu' => '16.35 - 17.00', 'tim1' => $p3, 'tim2' => $p4],
            ],
        ],
        [
            'tanggal' => '2025-09-13',
            'label'   => 'Sabtu, 13 September 2025',
            'rentang' => '16.00 - 17.00',
            'pertandingan' => [
                ['no' => 3, 'waktu' => '16.00 - 16.25', 'tim1' => $p4, 'tim2' => $p1],
                ['no' => 4, 'waktu' => '16.35 - 17.00', 'tim1' => $p2, 'tim2' => $p3],
            ],
        ],
        [
            'tanggal' => '2025-09-14',
            'label'   => 'Minggu, 14 September 2025',
            'rentang' => '16.00 - 17.00',
            'pertandingan' => [
                ['no' => 5, 'waktu' => '16.00 - 16.25', 'tim1' => $p1, 'tim2' => $p3],
                ['no' => 6, 'waktu' => '16.35 - 17.00', 'tim1' => $p4, 'tim2' => $p2],
            ],
        ],
    ];

    // --- Simpan ke tabel jadwal_soccer ---
    foreach ($jadwal as $hari) {
        foreach ($hari['pertandingan'] as $p) {
            if ($p['tim1']['rw'] !== null && $p['tim2']['rw'] !== null) {
                $stmt = $conn->prepare("
                    INSERT INTO jadwal_soccer 
                      (pertandingan, tanggal, waktu, tim1_rw, tim2_rw, skor1, skor2) 
                    VALUES (?, ?, ?, ?, ?, NULL, NULL)
                ");
                if ($stmt) {
                    $no  = (int)$p['no'];
                    $tgl = $hari['tanggal'];
                    $wkt = $p['waktu'];
                    $t1  = (int)$p['tim1']['rw'];
                    $t2  = (int)$p['tim2']['rw'];
                    $stmt->bind_param("issii", $no, $tgl, $wkt, $t1, $t2);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    // Bila prepare gagal, debug singkat (bisa dihapus di produksi)
                    error_log("Prepare gagal: " . $conn->error);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Mini Soccer - Olympic Jabal An Nur 2025</title>
  <style>
    body { font-family: 'Segoe UI', Tahoma, sans-serif; margin:0; padding:0; background:#fff; color:#333; }
    .header-image { width: 100%; max-height:300px; object-fit:cover; }
    .container { max-width:850px; margin:30px auto; background: rgba(255,255,255,.95); padding:30px; border-radius:15px; box-shadow:0 10px 25px rgba(0,0,0,.3); }
    h1 { text-align:center; color:#333; margin-bottom:20px; }
    .register-link { display:block; text-align:center; margin:15px 0; color:#28a745; text-decoration:none; font-weight:bold; font-size:18px; }
    .register-link:hover { text-decoration:underline; }
    ul { line-height:1.8; padding-left:20px; }
    .rules, .schedule { background:#f8f9fa; padding:20px; margin-top:20px; border-radius:10px; }
    .rules { border-left:5px solid #007bff; }
    .schedule { border-left:5px solid #28a745; }
    .contact { margin-top:30px; background-color: rgba(0,0,0,.05); padding:15px; border-radius:10px; }
    .back-link { display:block; text-align:center; margin-top:30px; color:#007bff; text-decoration:none; font-weight:bold; }
    .back-link:hover { text-decoration:underline; }
  </style>
</head>
<body>

<img src="images/soccer_field.jpg" alt="Lapangan Sepak Bola" class="header-image">

<div class="container">
<h1>Mini Soccer - Olympic Jabal An Nur 2025</h1>

<a href="soccerregister.php" class="register-link">&rarr; Daftar Sekarang</a>

<div class="rules">
  <h2>Aturan Pertandingan Mini Soccer:</h2>
  <ul>
    <li>Durasi: <strong>2 x 10 menit</strong></li>
    <li>Lokasi: <strong>GOR LS Cicadas</strong></li>
    <li>Jumlah pemain: <strong>8 vs 8</strong> (termasuk kiper)</li>
    <li>Pergantian pemain bebas</li>
    <li>Wasit dari panitia</li>
    <li>Sanksi akumulasi kartu kuning/merah</li>
    <li>Sistem klasemen (round-robin)</li>
    <li><strong>Kuota maksimal: 4 tim</strong></li>
    <li>Istirahat antar babak: 3 menit</li>
    <li>Tidak ada offside</li>
    <li>Bola out dilempar</li>
    <li>Pendaftaran ditutup pada tanggal 5 September 2025, jam 19:00</li>
  </ul>
</div>

<div class="schedule">
  <h2>Jadwal Pertandingan:</h2>
  <?php if ($jumlahTim < 4): ?>
    <p><em>Jadwal akan ditentukan setelah 4 tim terdaftar. Silakan klik "Daftar Sekarang" untuk mendaftarkan tim Anda.</em></p>
  <?php else: ?>
    <?php foreach ($jadwal as $hari): ?>
      <h3><?php echo $hari['label']; ?> (<?php echo $hari['rentang']; ?>)</h3>
      <ul>
        <?php foreach ($hari['pertandingan'] as $p): ?>
          <li>
            Pertandingan <?php echo $p['no']; ?> (<?php echo $p['waktu']; ?>):
            <?php echo labelTim($p['tim1']); ?> vs <?php echo labelTim($p['tim2']); ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endforeach; ?>
    <h3>Pembagian Hadiah</h3>
    <p><strong>5 Oktober 2025</strong></p>
  <?php endif; ?>
</div>

<div class="contact">
  <p><strong>Contact Person:</strong><br>
  Pak John - <a href="https://wa.me/6281288529222" target="_blank">0812-8852-9222 (WhatsApp)</a></p>
</div>

<a href="index.php" class="back-link">&larr; Kembali ke Beranda</a>
</div>

</body>
</html>
<?php $conn->close(); ?>