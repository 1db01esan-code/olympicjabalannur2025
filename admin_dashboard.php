<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "turnamen_olimpiade";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// =====================
// Helper: amankan nama tabel (alnum + underscore)
// =====================
function safeIdent(string $name): string {
    if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
        die("Invalid identifier.");
    }
    return $name;
}

// =====================
// Helper: ambil daftar kolom sebuah tabel
// =====================
function getColumns(mysqli $conn, string $table): array {
    $table = safeIdent($table);
    $res = $conn->query("SHOW COLUMNS FROM `$table`");
    $cols = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) $cols[] = $row['Field'];
        $res->free();
    }
    return $cols;
}

// =====================
// Ambil peserta (default urut RW, fallback ke kolom pertama)
// =====================
function getPeserta(mysqli $conn, string $table): array {
    $table = safeIdent($table);
    $cols  = getColumns($conn, $table);

    $orderCol = in_array('rw', $cols) ? 'rw' : $cols[0];
    $sql = "SELECT * FROM `$table` ORDER BY `$orderCol` ASC";
    $res = $conn->query($sql);

    $data = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) $data[] = $row;
        $res->free();
    }
    return $data;
}

// =====================
// Ambil jadwal (urut khusus per struktur tabel)
// =====================
function getJadwal(mysqli $conn, string $table): array {
    $table = safeIdent($table);
    $cols  = getColumns($conn, $table);

    $order = "";
    if (in_array('ronde', $cols) && in_array('meja', $cols)) {
        $order = "ORDER BY `ronde` ASC, `meja` ASC";
    } elseif (in_array('tanggal', $cols) && in_array('pertandingan', $cols)) {
        $order = "ORDER BY `tanggal` ASC, `pertandingan` ASC";
    } elseif (in_array('pertandingan', $cols)) {
        $order = "ORDER BY `pertandingan` ASC";
    } elseif (in_array('tanggal', $cols)) {
        $order = "ORDER BY `tanggal` ASC";
    } else {
        $first = $cols[0] ?? '1';
        $order = $first !== '1' ? "ORDER BY `$first` ASC" : "";
    }

    $sql = "SELECT * FROM `$table` $order";
    $res = $conn->query($sql);

    $data = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) $data[] = $row;
        $res->free();
    }
    return $data;
}

// =====================
// Daftar cabor & tabelnya
// =====================
$cabor = [
    'Soccer'     => ['peserta' => 'peserta_soccer',     'jadwal' => 'jadwal_soccer'],
    'Basketball' => ['peserta' => 'peserta_basket',     'jadwal' => 'jadwal_basket'],
    'Chess'      => ['peserta' => 'peserta_chess',      'jadwal' => 'jadwal_chess'],
    'Archery'    => ['peserta' => 'peserta_archery',    'jadwal' => 'jadwal_archery'],
    'Volleyball' => ['peserta' => 'peserta_volleyball', 'jadwal' => 'jadwal_volleyball'],
];

// Utility untuk ambil nilai kunci peserta
function keyPeserta(array $row): ?string {
    if (isset($row['rw'])) return (string)$row['rw'];
    if (isset($row['id'])) return (string)$row['id'];
    foreach ($row as $k => $v) return (string)$v;
    return null;
}

// Utility untuk membentuk id jadwal per baris
function keyJadwal(array $row): ?string {
    if (isset($row['ronde']) && isset($row['meja'])) {
        return $row['ronde'] . '_' . $row['meja'];
    }
    if (isset($row['pertandingan'])) {
        return (string)$row['pertandingan'];
    }
    foreach ($row as $k => $v) return (string)$v;
    return null;
}

// =====================
// Khusus: generate otomatis jadwal Basketball (knockout)
// =====================
$pesertaB = [];
$resB = $conn->query("SELECT rw FROM peserta_basket ORDER BY rw ASC LIMIT 8");
while ($row = $resB->fetch_assoc()) {
    $pesertaB[] = $row['rw'];
}
$jmlB = count($pesertaB);

if ($jmlB < 8) {
    $conn->query("TRUNCATE TABLE jadwal_basket");
} elseif ($jmlB == 8) {
    $conn->query("TRUNCATE TABLE jadwal_basket");
    $p = $pesertaB;

    $jadwalB = [
        // Penyisihan
        ['pertandingan'=>1,'ronde'=>'Penyisihan','tim_a'=>$p[7],'tim_b'=>$p[3],'tanggal'=>'2025-09-06','waktu'=>'07.30 - 07.55'],
        ['pertandingan'=>2,'ronde'=>'Penyisihan','tim_a'=>$p[4],'tim_b'=>$p[0],'tanggal'=>'2025-09-06','waktu'=>'08.05 - 08.30'],
        ['pertandingan'=>3,'ronde'=>'Penyisihan','tim_a'=>$p[2],'tim_b'=>$p[5],'tanggal'=>'2025-09-06','waktu'=>'08.40 - 09.05'],
        ['pertandingan'=>4,'ronde'=>'Penyisihan','tim_a'=>$p[1],'tim_b'=>$p[6],'tanggal'=>'2025-09-06','waktu'=>'09.15 - 09.40'],
        // Semifinal
        ['pertandingan'=>5,'ronde'=>'Semifinal','tim_a'=>'Pemenang 1','tim_b'=>'Pemenang 2','tanggal'=>'2025-09-13','waktu'=>'16.00 - 16.30'],
        ['pertandingan'=>6,'ronde'=>'Semifinal','tim_a'=>'Pemenang 3','tim_b'=>'Pemenang 4','tanggal'=>'2025-09-13','waktu'=>'16.45 - 17.15'],
        // Final
        ['pertandingan'=>7,'ronde'=>'Final','tim_a'=>'Pemenang Semifinal 1','tim_b'=>'Pemenang Semifinal 2','tanggal'=>'2025-09-14','waktu'=>'17.00 - 17.30'],
    ];

    $stmtB = $conn->prepare("
        INSERT INTO jadwal_basket (pertandingan, ronde, tim_a, tim_b, tanggal, waktu, skor_a, skor_b)
        VALUES (?,?,?,?,?,?,NULL,NULL)
    ");
    foreach ($jadwalB as $jb) {
        $stmtB->bind_param(
            "isssss",
            $jb['pertandingan'],
            $jb['ronde'],
            $jb['tim_a'],
            $jb['tim_b'],
            $jb['tanggal'],
            $jb['waktu']
        );
        $stmtB->execute();
    }
    $stmtB->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - Turnamen Olimpiade</title>
<style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f7f9fc; color: #333; margin: 20px; }
    h1 { text-align: center; color: #2c3e50; }
    h2 { margin-top: 20px; color: #34495e; border-bottom: 2px solid #3498db; padding-bottom: 5px; }
    h3 { margin-top: 15px; color: #555; }
    .card { background:#fff; padding:20px; margin:20px 0; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.05); }
    .table-wrapper { overflow-x: auto; }
    table { border-collapse: collapse; width: 100%; margin-bottom: 20px; background-color: #fff; min-width: 600px; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; white-space: nowrap; }
    th { background-color: #3498db; color: #fff; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    tr:hover { background-color: #f1f7ff; }
    .aksi a { display:inline-block; padding:5px 10px; margin-right:5px; text-decoration:none; border-radius:4px; font-size:0.9em; transition:0.2s; }
    .aksi a:hover { opacity:0.8; }
    .edit { background-color:#27ae60; color:#fff; }
    .delete { background-color:#e74c3c; color:#fff; }
</style>
</head>
<body>

<h1>Admin Dashboard - Turnamen Olimpiade</h1>
<div style="text-align:center; margin-bottom:20px;">
    <a href="admin_logout.php" 
       style="background:#e74c3c; color:#fff; padding:10px 20px; border-radius:6px; text-decoration:none;">
       Logout
    </a>
</div>

<?php foreach ($cabor as $namaCabor => $tables): ?>
<?php
    $tPeserta = safeIdent($tables['peserta']);
    $tJadwal  = safeIdent($tables['jadwal']);
    $peserta  = getPeserta($conn, $tPeserta);
    $jadwal   = getJadwal($conn,  $tJadwal);
?>
<div class="card">
    <h2><?= htmlspecialchars($namaCabor) ?></h2>

    <!-- Peserta -->
    <h3>Peserta</h3>
    <?php if (!empty($peserta)): ?>
        <div class="table-wrapper">
        <table>
            <tr>
                <?php foreach(array_keys($peserta[0]) as $col): ?>
                    <th><?= htmlspecialchars($col) ?></th>
                <?php endforeach; ?>
                <th>Aksi</th>
            </tr>
            <?php foreach($peserta as $row): ?>
                <tr>
                    <?php foreach($row as $val): ?>
                        <td><?= htmlspecialchars((string)$val) ?></td>
                    <?php endforeach; ?>
                    <td class="aksi">
                        <?php $id = keyPeserta($row); ?>
                        <?php if ($id !== null): ?>
                            <a class="edit" href="edit.php?tabel=<?= urlencode($tPeserta) ?>&id=<?= urlencode($id) ?>">Edit</a>
                            <a class="delete" href="delete.php?tabel=<?= urlencode($tPeserta) ?>&id=<?= urlencode($id) ?>" onclick="return confirm('Yakin ingin dihapus?')">Hapus</a>
                        <?php else: ?>
                            <span>-</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        </div>
    <?php else: ?>
        <p>Belum ada peserta.</p>
    <?php endif; ?>

    <!-- Jadwal -->
    <h3>Jadwal</h3>
    <?php if (!empty($jadwal)): ?>
        <div class="table-wrapper">
        <table>
            <tr>
                <?php foreach(array_keys($jadwal[0]) as $col): ?>
                    <th><?= htmlspecialchars($col) ?></th>
                <?php endforeach; ?>
                <th>Aksi</th>
            </tr>
            <?php foreach($jadwal as $row): ?>
                <tr>
                    <?php foreach($row as $col => $val): ?>
                        <?php if (stripos($col, 'skor') !== false && $val === null): ?>
                            <td>-</td>
                        <?php else: ?>
                            <td><?= htmlspecialchars((string)$val) ?></td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <td class="aksi">
                        <?php
                            if (isset($row['ronde']) && isset($row['meja'])) {
                                $id = $row['ronde'] . '_' . $row['meja'];
                            } elseif (isset($row['pertandingan'])) {
                                $id = (string)$row['pertandingan'];
                            } else {
                                $id = keyJadwal($row);
                            }
                        ?>
                        <?php if ($id !== null): ?>
                            <a class="edit" href="edit.php?tabel=<?= urlencode($tJadwal) ?>&id=<?= urlencode($id) ?>">Edit</a>
                            <a class="delete" href="delete.php?tabel=<?= urlencode($tJadwal) ?>&id=<?= urlencode($id) ?>" onclick="return confirm('Yakin ingin dihapus?')">Hapus</a>
                        <?php else: ?>
                            <span>-</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        </div>
    <?php else: ?>
        <p>Belum ada jadwal.</p>
    <?php endif; ?>
</div>
<?php endforeach; ?>

</body>
</html>