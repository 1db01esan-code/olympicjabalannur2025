<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "turnamen_olimpiade";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);
$conn->set_charset("utf8mb4");

/* ========== Fungsi Generic ========== */
function getSchedule($conn, $table){
    $res = $conn->query("SHOW TABLES LIKE '{$conn->real_escape_string($table)}'");
    if(!$res || $res->num_rows === 0) return false;
    return $conn->query("SELECT * FROM `{$conn->real_escape_string($table)}`");
}

/* ========== Mini Soccer (Liga) ========== */
function renderSoccerStandings($conn){
    $res = getSchedule($conn,"jadwal_soccer");
    if(!$res) return;

    $matches = [];
    while($r = $res->fetch_assoc()) $matches[] = $r;
    if(empty($matches)) return;

    echo "<div class='card'><h3>Mini Soccer - Hasil Pertandingan</h3>";
    echo "<table class='table-modern'><thead><tr>
        <th>Pertandingan</th><th>Tanggal</th><th>Waktu</th>
        <th>Tim 1</th><th>Skor</th><th>Tim 2</th>
    </tr></thead><tbody>";

    $table = [];

    foreach($matches as $row){
        $s1 = $row['skor1']; $s2 = $row['skor2'];
        $t1 = $row['tim1_rw'] ?? '';
        $t2 = $row['tim2_rw'] ?? '';

        echo "<tr>
          <td>".htmlspecialchars($row['pertandingan'])."</td>
          <td>".htmlspecialchars($row['tanggal'])."</td>
          <td>".htmlspecialchars($row['waktu'])."</td>
          <td>".htmlspecialchars($t1)."</td>
          <td>".(($s1 !== null && $s2 !== null) ? htmlspecialchars($s1." - ".$s2) : "-")."</td>
          <td>".htmlspecialchars($t2)."</td>
        </tr>";

        foreach([$t1, $t2] as $t){
            if($t !== '' && !isset($table[$t])){
                $table[$t] = ["main"=>0,"menang"=>0,"seri"=>0,"kalah"=>0,"gol"=>0,"kebobolan"=>0,"poin"=>0];
            }
        }

        // hitung poin & statistik hanya jika skor sudah terisi
        if($t1 !== '' && $t2 !== '' && $s1 !== null && $s2 !== null){
            $s1 = (int)$s1; $s2 = (int)$s2;
            $table[$t1]["main"]++; $table[$t2]["main"]++;
            $table[$t1]["gol"] += $s1; $table[$t1]["kebobolan"] += $s2;
            $table[$t2]["gol"] += $s2; $table[$t2]["kebobolan"] += $s1;

            if($s1 > $s2){
                $table[$t1]["menang"]++; $table[$t1]["poin"] += 3;
                $table[$t2]["kalah"]++;
            } elseif($s2 > $s1){
                $table[$t2]["menang"]++; $table[$t2]["poin"] += 3;
                $table[$t1]["kalah"]++;
            } else {
                $table[$t1]["seri"]++; $table[$t2]["seri"]++;
                $table[$t1]["poin"]++; $table[$t2]["poin"]++;
            }
        }
    }
    echo "</tbody></table></div>";

    if(!empty($table)){
        uasort($table, function($a, $b){
            $cmp = ($b['poin'] ?? 0) <=> ($a['poin'] ?? 0);
            if($cmp !== 0) return $cmp;
            $gdA = ($a['gol'] ?? 0) - ($a['kebobolan'] ?? 0);
            $gdB = ($b['gol'] ?? 0) - ($b['kebobolan'] ?? 0);
            return $gdB <=> $gdA;
        });

        echo "<div class='card'><h3>Mini Soccer - Klasemen</h3>";
        echo "<table class='table-modern'><thead><tr>
          <th>Tim</th><th>Main</th><th>Menang</th><th>Seri</th><th>Kalah</th>
          <th>Gol</th><th>Kebobolan</th><th>Selisih</th><th>Poin</th>
        </tr></thead><tbody>";

        foreach($table as $team => $d){
            $main = $d['main'] ?? 0;
            $menang = $d['menang'] ?? 0;
            $seri = $d['seri'] ?? 0;
            $kalah = $d['kalah'] ?? 0;
            $gol = $d['gol'] ?? 0;
            $keb = $d['kebobolan'] ?? 0;
            $poin = $d['poin'] ?? 0;
            $sel = $gol - $keb;
            echo "<tr>
              <td>".htmlspecialchars($team)."</td>
              <td>{$main}</td><td>{$menang}</td><td>{$seri}</td><td>{$kalah}</td>
              <td>{$gol}</td><td>{$keb}</td><td>{$sel}</td><td>{$poin}</td>
            </tr>";
        }

        echo "</tbody></table></div>";
    }
}

/* ========== Bola Voli ========== */
function renderVolleyballStandings($conn){
    $res = getSchedule($conn,"jadwal_volleyball");
    if(!$res) return;

    $matches = [];
    while($r = $res->fetch_assoc()) $matches[] = $r;
    if(empty($matches)) return;

    echo "<div class='card'><h3>Bola Voli - Hasil Pertandingan</h3>";
    echo "<table class='table-modern'><thead><tr>
        <th>Pertandingan</th><th>Tanggal</th><th>Waktu</th>
        <th>Tim A</th><th>Skor</th><th>Tim B</th>
    </tr></thead><tbody>";

    $table = [];
    foreach($matches as $row){
        $t1 = $row['tim_a'] ?? '';
        $t2 = $row['tim_b'] ?? '';
        $s1 = $row['skor_a']; $s2 = $row['skor_b'];

        echo "<tr>
          <td>".htmlspecialchars($row['pertandingan'])."</td>
          <td>".htmlspecialchars($row['tanggal'])."</td>
          <td>".htmlspecialchars($row['waktu'])."</td>
          <td>".htmlspecialchars($t1)."</td>
          <td>".(($s1!==null && $s2!==null) ? htmlspecialchars($s1." - ".$s2) : "-")."</td>
          <td>".htmlspecialchars($t2)."</td>
        </tr>";

        foreach([$t1,$t2] as $t){
            if($t !== '' && !isset($table[$t])){
                $table[$t] = ["main"=>0,"menang"=>0,"kalah"=>0,"poin"=>0];
            }
        }

        if($t1 !== '' && $t2 !== '' && $s1 !== null && $s2 !== null){
            $s1 = (int)$s1; $s2 = (int)$s2;
            $table[$t1]['main']++; $table[$t2]['main']++;

            if($s1 == 2 && $s2 == 0){
                $table[$t1]['menang']++; $table[$t1]['poin'] += 3; $table[$t2]['kalah']++;
            } elseif($s1 == 2 && $s2 == 1){
                $table[$t1]['menang']++; $table[$t1]['poin'] += 2; $table[$t2]['kalah']++;
            } elseif($s2 == 2 && $s1 == 0){
                $table[$t2]['menang']++; $table[$t2]['poin'] += 3; $table[$t1]['kalah']++;
            } elseif($s2 == 2 && $s1 == 1){
                $table[$t2]['menang']++; $table[$t2]['poin'] += 2; $table[$t1]['kalah']++;
            }
        }
    }
    echo "</tbody></table></div>";

    if(!empty($table)){
        uasort($table, function($a,$b){
            return ($b['poin'] ?? 0) <=> ($a['poin'] ?? 0);
        });

        echo "<div class='card'><h3>Bola Voli - Klasemen</h3>";
        echo "<table class='table-modern'><thead><tr>
          <th>Tim</th><th>Main</th><th>Menang</th><th>Kalah</th><th>Poin</th>
        </tr></thead><tbody>";

        foreach($table as $team=>$d){
            echo "<tr>
              <td>".htmlspecialchars($team)."</td>
              <td>{$d['main']}</td><td>{$d['menang']}</td><td>{$d['kalah']}</td><td>{$d['poin']}</td>
            </tr>";
        }

        echo "</tbody></table></div>";
    }
}

/* ========== Catur ========== */
function renderChessStandings($conn){
    $res = getSchedule($conn,"jadwal_chess");
    if(!$res) return;

    $matches = [];
    while($r = $res->fetch_assoc()) $matches[] = $r;
    if(empty($matches)) return;

    echo "<div class='card'><h3>Catur - Hasil Pertandingan</h3>";
    echo "<table class='table-modern'><thead><tr>
        <th>Ronde</th><th>Meja</th><th>Pemain 1</th><th>Skor 1</th><th>Pemain 2</th><th>Skor 2</th>
    </tr></thead><tbody>";

    $table = [];
    foreach($matches as $row){
        $p1 = $row['pemain1'] ?? '';
        $p2 = $row['pemain2'] ?? '';
        $s1 = $row['skor1']; $s2 = $row['skor2'];

        echo "<tr>
          <td>".htmlspecialchars($row['ronde'])."</td>
          <td>".htmlspecialchars($row['meja'])."</td>
          <td>".htmlspecialchars($p1)."</td><td>".($s1!==null ? htmlspecialchars($s1) : "-")."</td>
          <td>".htmlspecialchars($p2)."</td><td>".($s2!==null ? htmlspecialchars($s2) : "-")."</td>
        </tr>";

        foreach([$p1,$p2] as $p){
            if($p !== '' && !isset($table[$p])){
                $table[$p] = ["main"=>0,"poin"=>0.0];
            }
        }

        if($p1 !== '' && $p2 !== '' && $s1 !== null && $s2 !== null){
            $table[$p1]['main']++; $table[$p2]['main']++;
            $table[$p1]['poin'] += (float)$s1;
            $table[$p2]['poin'] += (float)$s2;
        }
    }

    echo "</tbody></table></div>";

    if(!empty($table)){
        uasort($table, fn($a,$b) => ($b['poin'] ?? 0) <=> ($a['poin'] ?? 0));

        echo "<div class='card'><h3>Catur - Klasemen</h3>";
        echo "<table class='table-modern'><thead><tr>
            <th>Pemain</th><th>Main</th><th>Poin</th>
        </tr></thead><tbody>";
        foreach($table as $pemain=>$d){
            echo "<tr><td>".htmlspecialchars($pemain)."</td><td>{$d['main']}</td><td>{$d['poin']}</td></tr>";
        }
        echo "</tbody></table></div>";
    }
}

/* ========== Basket ========== */
function renderBasketStandings($conn){
    $res = getSchedule($conn,"jadwal_basket");
    if(!$res) return;

    echo "<div class='card'><h3>Bola Basket (Knockout)</h3>";
    echo "<table class='table-modern'><thead><tr>
        <th>Pertandingan</th><th>Ronde</th><th>Tim A</th><th>Skor A</th>
        <th>Tim B</th><th>Skor B</th><th>Tanggal</th><th>Waktu</th>
    </tr></thead><tbody>";
    while($row = $res->fetch_assoc()){
        echo "<tr>
          <td>".htmlspecialchars($row['pertandingan'])."</td>
          <td>".htmlspecialchars($row['ronde'])."</td>
          <td>".htmlspecialchars($row['tim_a'])."</td><td>".($row['skor_a']!==null ? htmlspecialchars($row['skor_a']) : "-")."</td>
          <td>".htmlspecialchars($row['tim_b'])."</td><td>".($row['skor_b']!==null ? htmlspecialchars($row['skor_b']) : "-")."</td>
          <td>".htmlspecialchars($row['tanggal'])."</td><td>".htmlspecialchars($row['waktu'])."</td>
        </tr>";
    }
    echo "</tbody></table></div>";
}

/* ========== Archery ========== */
function renderArcheryStandings($conn){
    $res = getSchedule($conn,"jadwal_archery");
    if(!$res) return;

    echo "<div class='card'><h3>Panahan - Hasil Pertandingan</h3>";
    echo "<table class='table-modern'><thead><tr>
        <th>Pertandingan</th><th>Nama Pertandingan</th>
        <th>Pemanah 1</th><th>Skor 1</th>
        <th>Pemanah 2</th><th>Skor 2</th>
        <th>Pemanah 3</th><th>Skor 3</th>
        <th>Pemanah 4</th><th>Skor 4</th>
    </tr></thead><tbody>";
    while($row = $res->fetch_assoc()){
        echo "<tr>
          <td>".htmlspecialchars($row['pertandingan'])."</td>
          <td>".htmlspecialchars($row['nama_pertandingan'])."</td>
          <td>".htmlspecialchars($row['peserta1'])."</td><td>".($row['skor1']!==null ? htmlspecialchars($row['skor1']) : "-")."</td>
          <td>".htmlspecialchars($row['peserta2'])."</td><td>".($row['skor2']!==null ? htmlspecialchars($row['skor2']) : "-")."</td>
          <td>".htmlspecialchars($row['peserta3'])."</td><td>".($row['skor3']!==null ? htmlspecialchars($row['skor3']) : "-")."</td>
          <td>".htmlspecialchars($row['peserta4'])."</td><td>".($row['skor4']!==null ? htmlspecialchars($row['skor4']) : "-")."</td>
        </tr>";
    }
    echo "</tbody></table></div>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hasil Pertandingan - Olympic Jabal An Nur 2025</title>
<style>
body{font-family:Arial,sans-serif;margin:0;padding:0;background:#f4f6f8;color:#333;}
.container{max-width:1200px;margin:20px auto;padding:20px;}
h1{text-align:center;color:#007bff;margin-bottom:5px;}
.card{background:white;margin:20px 0;padding:20px;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,0.1);}
.table-modern{width:100%;border-collapse:collapse;margin-top:10px;}
.table-modern th, .table-modern td{border:1px solid #ddd;padding:10px;text-align:center;}
.table-modern th{background:#007bff;color:white;}
.muted{color:#777;font-style:italic;}
@media(max-width:768px){.table-modern th,.table-modern td{font-size:0.8rem;padding:6px;}}
</style>
</head>
<body>
<div class="container">
<h1>Hasil Pertandingan Olympic Jabal An Nur 2025</h1>

<?php
renderSoccerStandings($conn);
renderVolleyballStandings($conn);
renderChessStandings($conn);
renderBasketStandings($conn);
renderArcheryStandings($conn);
?>
