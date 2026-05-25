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
$conn->set_charset("utf8mb4");

// Ambil semua peserta (maksimal 8 RW)
$peserta = [];
$result = $conn->query("SELECT rw FROM peserta_basket ORDER BY rw ASC LIMIT 8");
while ($row = $result->fetch_assoc()) {
    $peserta[] = $row['rw'];
}
$jumlahTim = count($peserta);

// Jika sudah ada 8 peserta, pastikan jadwal knockout terbuat
if ($jumlahTim == 8) {
    $cek = $conn->query("SELECT COUNT(*) AS jml FROM jadwal_basket");
    $row = $cek->fetch_assoc();
    if ($row['jml'] == 0) {
        // Reset jadwal lama
        $conn->query("TRUNCATE TABLE jadwal_basket");

        // Susun jadwal knockout
        $jadwal = [
            // Babak Penyisihan
            [1, "Babak Penyisihan - Sabtu 6 September 2025", $peserta[7], $peserta[3], "2025-09-06", "07:30 - 07:55"],
            [2, "Babak Penyisihan - Sabtu 6 September 2025", $peserta[4], $peserta[0], "2025-09-06", "08:05 - 08:30"],
            [3, "Babak Penyisihan - Sabtu 6 September 2025", $peserta[2], $peserta[5], "2025-09-06", "08:40 - 09:05"],
            [4, "Babak Penyisihan - Sabtu 6 September 2025", $peserta[1], $peserta[6], "2025-09-06", "09:15 - 09:40"],
            // Semifinal
            [5, "Semifinal - Sabtu 13 September 2025", "Pemenang 1", "Pemenang 2", "2025-09-13", "16:00 - 16:30"],
            [6, "Semifinal - Sabtu 13 September 2025", "Pemenang 3", "Pemenang 4", "2025-09-13", "16:45 - 17:15"],
            // Final
            [7, "Final - Minggu 14 September 2025", "Pemenang Semifinal 1", "Pemenang Semifinal 2", "2025-09-14", "17:00 - 17:30"],
        ];

        $stmt = $conn->prepare("
            INSERT INTO jadwal_basket (pertandingan, ronde, tim_a, tim_b, tanggal, waktu, skor_a, skor_b)
            VALUES (?,?,?,?,?,?,0,0)
        ");
        foreach ($jadwal as $j) {
            $stmt->bind_param("isssss", $j[0], $j[1], $j[2], $j[3], $j[4], $j[5]);
            $stmt->execute();
        }
        $stmt->close();
    }
}

// Ambil jadwal dari database
$jadwalResult = $conn->query("SELECT * FROM jadwal_basket ORDER BY pertandingan ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bola Basket - Olympic Jabal An Nur 2025</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin:0; padding:0; background:#fff; color:#333; }
        .header-image { width:100%; max-height:300px; object-fit:cover; }
        .container { max-width:850px; margin:30px auto; background:#f5f5f5; padding:30px; border-radius:15px; box-shadow:0 10px 25px rgba(0,0,0,0.1); }
        h1 { text-align:center; color:#444; margin-bottom:20px; }
        .rules h2, .schedule h2 { color:#dc2430; }
        ul { line-height:1.8; padding-left:20px; }
        .contact { margin-top:30px; background:#eee; padding:15px; border-radius:10px; }
        .contact strong { color:#dc2430; }
        .back-link { display:block; text-align:center; margin-top:30px; color:#dc2430; text-decoration:none; font-weight:bold; }
        .back-link:hover { text-decoration:underline; }
        .register-btn { display:inline-block; padding:12px 20px; background:#ffeb3b; color:#000; text-decoration:none; font-weight:bold; border-radius:8px; }
        .register-btn:hover { background:#fdd835; }
        .schedule { background:#fff; padding:20px; margin-top:20px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
        table { width:100%; border-collapse:collapse; margin-top:15px; }
        th, td { border:1px solid #ccc; padding:8px; text-align:center; }
        th { background:#f2f2f2; }
        .day-label { background:#ffe0e0; font-weight:bold; text-align:left; padding:8px; }
    </style>
</head>
<body>

<img src="images/basketball_court.jpg" alt="Lapangan Bola Basket" class="header-image">

<div class="container">
    <h1>Bola Basket - Olympic Jabal An Nur 2025</h1>

    <div style="text-align:center; margin-bottom:20px;">
        <a href="basketballregister.php" class="register-btn">Daftar Sekarang</a>
    </div>

    <!-- Aturan -->
    <div class="rules">
        <h2>Aturan Pertandingan Bola Basket:</h2>
        <ul>
            <li>Bermain selama <strong>2 x 7 menit</strong></li>
            <li>Lokasi: <strong>Lapangan Basket halaman Masjid Jami Jabal An-Nur</strong></li>
            <li>Jumlah pemain: <strong>3 vs 3</strong></li>
            <li>Menggunakan <strong>1 ring</strong></li>
            <li>Skor:
                <ul>
                    <li>Tembakan dari dalam lingkaran dekat ring = <strong>1 poin</strong></li>
                    <li>Tembakan dari luar lingkaran = <strong>2 poin</strong></li>
                </ul>
            </li>
            <li>Wasit: <strong>semi-profesional</strong></li>
            <li>Time-break selama <strong>30 detik</strong></li>
            <li>Jika tim mencapai skor <strong>21</strong>, otomatis menjadi pemenang</li>
            <li>Pertandingan menggunakan <strong>sistem gugur</strong></li>
            <li>Jika skor imbang, overtime <strong>2 menit</strong></li>
            <li>Pendaftaran ditutup pada tanggal 5 September 2025, jam 19:00</li>
        </ul>
    </div>

    <!-- Jadwal Pertandingan -->
    <div class="schedule">
        <h2>Jadwal Pertandingan:</h2>

        <?php if ($jumlahTim < 8 || $jadwalResult->num_rows == 0): ?>
            <p><em>Jadwal pertandingan akan muncul setelah 8 RW terdaftar dan jadwal dibuat otomatis.</em></p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Pertandingan</th>
                    <th>Tim</th>
                    <th>Waktu</th>
                </tr>
                <?php 
                $currentRonde = "";
                while($row = $jadwalResult->fetch_assoc()):
                    if($currentRonde != $row['ronde']):
                        $currentRonde = $row['ronde'];
                        echo "<tr><td colspan='3' class='day-label'>".$currentRonde."</td></tr>";
                    endif;
                ?>
                <tr>
                    <td>Pertandingan <?= $row['pertandingan']; ?></td>
                    <td><?= htmlspecialchars($row['tim_a']); ?> vs <?= htmlspecialchars($row['tim_b']); ?></td>
                    <td><?= $row['tanggal'] . " " . $row['waktu']; ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
            <p style="margin-top:10px; font-style:italic;">
                *Jadwal dapat berubah sesuai kondisi lapangan & cuaca.
            </p>
        <?php endif; ?>
    </div>

    <!-- Kontak -->
    <div class="contact">
        <p>
            <strong>Contact Person:</strong><br>
            Pak Koen - 
            <a href="https://wa.me/6281806796494" style="color:#dc2430; text-decoration:underline;" target="_blank">
                0818-0679-6494 (WhatsApp)
            </a>
        </p>
    </div>

    <a href="index.php" class="back-link">← Kembali ke Beranda</a>
</div>

</body>
</html>

<?php $conn->close(); ?>