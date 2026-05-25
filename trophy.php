<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "turnamen_olimpiade";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }
$conn->set_charset("utf8mb4");

/** =============================
 *  Fungsi Liga (Soccer, Voli, Chess)
 *  ============================= */
function getKlasemenSoccer($conn) {
    $cekTotal = $conn->query("SELECT COUNT(*) AS total FROM jadwal_soccer");
    $rowTotal = $cekTotal->fetch_assoc();
    if ($rowTotal['total'] == 0) return "belum_dimulai";

    $cek = $conn->query("SELECT COUNT(*) AS belum FROM jadwal_soccer WHERE skor1 IS NULL OR skor2 IS NULL");
    $row = $cek->fetch_assoc();
    if ($row['belum'] > 0) return "belum_selesai";

    $res = $conn->query("SELECT tim1_rw AS tim1, tim2_rw AS tim2, skor1, skor2 FROM jadwal_soccer");
    $klasemen = [];
    while ($row = $res->fetch_assoc()) {
        $t1=$row['tim1']; $t2=$row['tim2']; $s1=(int)$row['skor1']; $s2=(int)$row['skor2'];
        if (!isset($klasemen[$t1])) $klasemen[$t1] = ["poin"=>0,"gm"=>0,"gk"=>0];
        if (!isset($klasemen[$t2])) $klasemen[$t2] = ["poin"=>0,"gm"=>0,"gk"=>0];
        $klasemen[$t1]["gm"]+=$s1; $klasemen[$t1]["gk"]+=$s2;
        $klasemen[$t2]["gm"]+=$s2; $klasemen[$t2]["gk"]+=$s1;
        if ($s1>$s2) $klasemen[$t1]["poin"]+=3; 
        elseif ($s1<$s2) $klasemen[$t2]["poin"]+=3; 
        else { $klasemen[$t1]["poin"]++; $klasemen[$t2]["poin"]++; }
    }
    uasort($klasemen, fn($a,$b)=>($b["poin"]<=>$a["poin"]) ?: (($b["gm"]-$b["gk"])<=>($a["gm"]-$a["gk"])));
    return $klasemen;
}
function getKlasemenVolleyball($conn) {
    $cekTotal = $conn->query("SELECT COUNT(*) AS total FROM jadwal_volleyball");
    $rowTotal = $cekTotal->fetch_assoc();
    if ($rowTotal['total'] == 0) return "belum_dimulai";

    $cek = $conn->query("SELECT COUNT(*) AS belum FROM jadwal_volleyball WHERE skor_a IS NULL OR skor_b IS NULL");
    $row = $cek->fetch_assoc();
    if ($row['belum'] > 0) return "belum_selesai";

    $res = $conn->query("SELECT tim_a AS tim1, tim_b AS tim2, skor_a AS skor1, skor_b AS skor2 FROM jadwal_volleyball");
    $klasemen=[];
    while ($row=$res->fetch_assoc()) {
        $t1=$row['tim1']; $t2=$row['tim2']; $s1=(int)$row['skor1']; $s2=(int)$row['skor2'];
        if (!isset($klasemen[$t1])) $klasemen[$t1] = ["poin"=>0];
        if (!isset($klasemen[$t2])) $klasemen[$t2] = ["poin"=>0];
        if ($s1>$s2) $klasemen[$t1]["poin"]+=2; 
        elseif ($s2>$s1) $klasemen[$t2]["poin"]+=2;
    }
    uasort($klasemen, fn($a,$b)=>$b["poin"]<=>$a["poin"]);
    return $klasemen;
}
function getKlasemenChess($conn) {
    $cekTotal = $conn->query("SELECT COUNT(*) AS total FROM jadwal_chess");
    $rowTotal = $cekTotal->fetch_assoc();
    if ($rowTotal['total'] == 0) return "belum_dimulai";

    $cek = $conn->query("SELECT COUNT(*) AS belum FROM jadwal_chess WHERE skor1 IS NULL OR skor2 IS NULL");
    $row = $cek->fetch_assoc();
    if ($row['belum'] > 0) return "belum_selesai";

    $res=$conn->query("SELECT pemain1,pemain2,skor1,skor2 FROM jadwal_chess");
    $klasemen=[];
    while($row=$res->fetch_assoc()){
        $p1=$row['pemain1']; $p2=$row['pemain2']; $s1=(float)$row['skor1']; $s2=(float)$row['skor2'];
        if (!isset($klasemen[$p1])) $klasemen[$p1] = ["poin"=>0];
        if (!isset($klasemen[$p2])) $klasemen[$p2] = ["poin"=>0];
        $klasemen[$p1]["poin"]+=$s1; $klasemen[$p2]["poin"]+=$s2;
    }
    uasort($klasemen, fn($a,$b)=>$b["poin"]<=>$a["poin"]);
    return $klasemen;
}

/** =============================
 *  Fungsi Knockout (Basket, Archery)
 *  ============================= */
function getJuaraBasket($conn) {
    $cekTotal = $conn->query("SELECT COUNT(*) AS total FROM jadwal_basket WHERE ronde='Final'");
    $rowTotal = $cekTotal->fetch_assoc();
    if ($rowTotal['total'] == 0) return "belum_dimulai";

    $final = $conn->query("SELECT tim_a,tim_b,skor_a,skor_b FROM jadwal_basket WHERE ronde='Final'");
    if ($final && $final->num_rows>0) {
        $f=$final->fetch_assoc();
        if ($f['skor_a']===null || $f['skor_b']===null) return "belum_selesai";
        $t1=$f['tim_a']; $t2=$f['tim_b']; $s1=(int)$f['skor_a']; $s2=(int)$f['skor_b'];
        if ($s1>$s2) return [$t1,$t2]; elseif ($s2>$s1) return [$t2,$t1];
    }
    return null;
}
function getJuaraArchery($conn) {
    $cekTotal = $conn->query("SELECT COUNT(*) AS total FROM jadwal_archery");
    $rowTotal = $cekTotal->fetch_assoc();
    if ($rowTotal['total'] == 0) return "belum_dimulai";

    $res=$conn->query("SELECT peserta1,peserta2,peserta3,peserta4,skor1,skor2,skor3,skor4 FROM jadwal_archery");
    if($res && $res->num_rows>0){
        $r=$res->fetch_assoc();
        if ($r['skor1']===null || $r['skor2']===null || $r['skor3']===null || $r['skor4']===null) return "belum_selesai";
        $peserta=[
            $r['peserta1']=>$r['skor1'],
            $r['peserta2']=>$r['skor2'],
            $r['peserta3']=>$r['skor3'],
            $r['peserta4']=>$r['skor4'],
        ];
        arsort($peserta);
        return array_keys($peserta);
    }
    return null;
}

/** =============================
 *  Render Juara
 *  ============================= */
function renderJuara($klasemen,$label) {
    echo "<h2>🏆 $label</h2>";
    if($klasemen==="belum_dimulai"){ echo "<p class='muted'>Kompetisi belum dimulai</p>"; return; }
    if($klasemen==="belum_selesai"){ echo "<p class='muted'>Kompetisi belum selesai</p>"; return; }
    if(empty($klasemen)){ echo "<p class='muted'>Tidak ada data</p>"; return; }
    $i=1;
    foreach($klasemen as $nama=>$d){
        $medal = $i==1 ? "🥇" : ($i==2 ? "🥈" : "🥉");
        echo "<p>$medal Juara $i: <strong>$nama</strong></p>";
        if(++$i>3) break;
    }
}
function renderJuaraKnockout($juara,$label) {
    echo "<h2>🏆 $label</h2>";
    if($juara==="belum_dimulai"){ echo "<p class='muted'>Kompetisi belum dimulai</p>"; return; }
    if($juara==="belum_selesai"){ echo "<p class='muted'>Final belum selesai</p>"; return; }
    if(empty($juara)){ echo "<p class='muted'>Tidak ada data</p>"; return; }
    echo "<p>🥇 Juara 1: <strong>{$juara[0]}</strong></p>";
    echo "<p>🥈 Juara 2: <strong>{$juara[1]}</strong></p>";
    if(isset($juara[2])) echo "<p>🥉 Juara 3: <strong>{$juara[2]}</strong></p>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pemenang Turnamen - Olympic Jabal An Nur 2025</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f9f9f9; margin:0; padding:0; }
    .container { max-width:800px; margin:30px auto; background:white; padding:20px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
    h1 { text-align:center; color:#007bff; }
    h2 { margin-top:30px; color:#444; }
    .muted { color:#777; font-style:italic; }
  </style>
</head>
<body>
  <div class="container">
    <h1>🏆 Pemenang Olympic Jabal An Nur 2025</h1>
    <?php
      // Liga
      renderJuara(getKlasemenSoccer($conn), "Sepak Bola");
      renderJuara(getKlasemenVolleyball($conn), "Bola Voli");
      renderJuara(getKlasemenChess($conn), "Catur");

      // Knockout
      renderJuaraKnockout(getJuaraBasket($conn), "Bola Basket");
      renderJuaraKnockout(getJuaraArchery($conn), "Panahan");
    ?>
  </div>
</body>
</html>
<?php $conn->close(); ?>