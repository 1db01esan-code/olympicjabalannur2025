<?php
// ==========================
// Koneksi DB
// ==========================
$host = "localhost";
$user = "root";
$pass = "";
$db   = "turnamen_olimpiade";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// ==========================
// Ambil peserta chess (max 8 orang) urut RW
// ==========================
$peserta = [];
$resPeserta = $conn->query("SELECT nama_peserta, rw FROM peserta_chess ORDER BY rw ASC LIMIT 8");
while ($row = $resPeserta->fetch_assoc()) {
    $peserta[] = $row;
}
$totalPeserta = count($peserta);

// ==========================
// Hapus jadwal lama jika peserta kurang dari 8
// ==========================
if ($totalPeserta < 8) {
    $conn->query("TRUNCATE TABLE jadwal_chess");
}

// ==========================
// Generate Jadwal otomatis jika peserta == 8
// ==========================
if ($totalPeserta == 8) {
    // Hapus jadwal lama agar tersusun ulang
    $conn->query("TRUNCATE TABLE jadwal_chess");

    // --- Ronde 1 isi nama ---
    $pairR1 = [
        [0,1,1], [2,3,2], [4,5,3], [6,7,4]
    ];
    $waktuR1 = "07.30 - 08.15";

    $stmt = $conn->prepare(
        "INSERT INTO jadwal_chess (ronde, meja, pemain1, pemain2, waktu, skor1, skor2) 
         VALUES (?,?,?,?,?,?,?)"
    );
    foreach ($pairR1 as $m) {
        $ronde = 1;
        $meja  = $m[2];
        $p1    = $peserta[$m[0]]['nama_peserta'];
        $p2    = $peserta[$m[1]]['nama_peserta'];
        $waktu = $waktuR1;
        $skor1 = 0;
        $skor2 = 0;

        $stmt->bind_param("iisssii", $ronde, $meja, $p1, $p2, $waktu, $skor1, $skor2);
        $stmt->execute();
    }
    $stmt->close();

    // --- Ronde 2–5 placeholder (kosong) ---
    $waktuPerRonde = [
        2 => "08.25 - 09.10",
        3 => "09.20 - 10.05",
        4 => "10.15 - 11.00",
        5 => "11.10 - 11.55",
    ];

    $stmtPH = $conn->prepare(
        "INSERT INTO jadwal_chess (ronde, meja, pemain1, pemain2, waktu, skor1, skor2) 
         VALUES (?,?,?,?,?,?,?)"
    );
    foreach ([2,3,4,5] as $r) {
        for ($meja = 1; $meja <= 4; $meja++) {
            $ronde = $r;
            $mejaNo = $meja;
            $p1 = "";
            $p2 = "";
            $waktu = $waktuPerRonde[$r];
            $skor1 = 0;
            $skor2 = 0;

            $stmtPH->bind_param("iisssii", $ronde, $mejaNo, $p1, $p2, $waktu, $skor1, $skor2);
            $stmtPH->execute();
        }
    }
    $stmtPH->close();
}

// ==========================
// Ambil jadwal untuk ditampilkan
// ==========================
$jadwal = [];
$resJ = $conn->query("SELECT * FROM jadwal_chess ORDER BY ronde ASC, meja ASC");
while ($row = $resJ->fetch_assoc()) {
    $jadwal[(int)$row['ronde']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Catur - Olympic Jabal An Nur 2025</title>
  <style>
    body { margin:0; font-family:Arial,sans-serif; background:#f2f2f2; color:#333; }
    .header { background:url('images/chess.jpg') no-repeat center center/cover; height:300px;
              display:flex; flex-direction:column; align-items:center; justify-content:center;
              color:white; text-shadow:2px 2px 4px #000; }
    .header h1 { font-size:40px; margin:0; }
    .header a { margin-top:20px; background:#007bff; padding:10px 20px; color:white;
                border-radius:5px; text-decoration:none; font-weight:bold; }
    .header a:hover { background:#0056b3; }
    .container { max-width:900px; margin:30px auto; background:white; padding:20px;
                 border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
    h2 { color:#007bff; }
    ul { line-height:1.7; padding-left:20px; }
    .contact { margin-top:30px; background:#e0f7fa; padding:15px;
               border-left:5px solid #007bff; border-radius:8px; }
    .contact strong { color:#007bff; }
    a.back { display:block; text-align:center; margin:30px auto 0; text-decoration:none;
             color:#007bff; font-weight:bold; }
    a.back:hover { text-decoration:underline; }
    table { width:100%; border-collapse:collapse; margin-top:15px; }
    th, td { border:1px solid #ccc; padding:8px; text-align:center; }
    th { background:#f2f2f2; }
    .note { font-style:italic; color:#666; margin-top:8px; }
    .badge-wait { display:inline-block; padding:2px 8px; border-radius:8px;
                  background:#fff3cd; color:#8a6d3b; font-size:12px; }
  </style>
</head>
<body>

  <div class="header">
    <h1>Catur - Olympic Jabal An Nur 2025</h1>
    <a href="chessregister.php">Daftar Catur</a>
  </div>

  <div class="container">
    <h2>Aturan Pertandingan</h2>
    <ul>
      <li>Menggunakan Sistem Swiss (5 ronde)</li>
      <li>Waktu per pertandingan: 45 menit</li>
      <li>Wasit didatangkan langsung dari <strong>PERCASI</strong></li>
      <li>Maksimal: 8 peserta</li>
      <li>Lokasi: <strong>Lantai bawah Masjid Jami Jabal An-Nur</strong></li>
      <li>Pendaftaran ditutup: 26 September 2025, 19:00</li>
    </ul>

    <!-- Peserta -->
    <h2>Peserta Terdaftar</h2>
    <?php if ($totalPeserta == 0): ?>
      <p><em>Belum ada peserta.</em></p>
    <?php else: ?>
      <ul>
        <?php $i=1; foreach ($peserta as $p): ?>
          <li><?= $i++; ?>. <?= htmlspecialchars($p['nama_peserta']); ?> (RW <?= htmlspecialchars($p['rw']); ?>)</li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <!-- Jadwal -->
    <h2>Jadwal Pertandingan</h2>
    <?php if ($totalPeserta < 8): ?>
      <p style="color:red; font-weight:bold;">Jadwal akan dibuat otomatis setelah 8 peserta terpenuhi.</p>
    <?php else: ?>
      <?php foreach ($jadwal as $ronde => $rows): ?>
        <h3>Ronde <?= $ronde; ?></h3>
        <table>
          <tr><th>Meja</th><th>Pemain 1</th><th>Pemain 2</th><th>Waktu</th></tr>
          <?php foreach ($rows as $m): 
            $p1 = trim($m['pemain1']);
            $p2 = trim($m['pemain2']);
            $empty = ($p1 === "" && $p2 === "");
          ?>
            <tr>
              <td><?= $m['meja']; ?></td>
              <td><?= $empty ? '<span class="badge-wait">Menunggu pairing</span>' : htmlspecialchars($p1); ?></td>
              <td><?= $empty ? '<span class="badge-wait">Menunggu pairing</span>' : htmlspecialchars($p2); ?></td>
              <td><?= htmlspecialchars($m['waktu']); ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
        <?php if ($ronde >= 2): ?>
          <div class="note">Pairing ronde <?= $ronde; ?> akan diisi setelah hasil ronde sebelumnya diproses.</div>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php endif; ?>

    <div class="contact">
      <strong>Contact Person:</strong><br>
      Pak Mujahidin Ali - <a href="https://wa.me/6281288529222" target="_blank">0812-8852-9222 (WhatsApp)</a>
    </div>

    <a href="index.php" class="back">← Kembali ke Beranda</a>
  </div>

</body>
</html>
<?php $conn->close(); ?>