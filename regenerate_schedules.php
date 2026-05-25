<?php
function regenerateAllSchedules($conn) {
    regenerateSoccer($conn);
    regenerateBasket($conn);
    regenerateVolleyball($conn);
    regenerateChess($conn);
    regenerateArchery($conn);
}

// ====================
// Soccer → sistem liga
// ====================
function regenerateSoccer($conn) {
    $conn->query("TRUNCATE TABLE jadwal_soccer");
    $teams = [];
    $res = $conn->query("SELECT rw FROM peserta_soccer ORDER BY rw ASC");
    while ($row = $res->fetch_assoc()) $teams[] = $row['rw'];

    $match = 1;
    $n = count($teams);
    for ($i = 0; $i < $n; $i++) {
        for ($j = $i + 1; $j < $n; $j++) {
            $tanggal = date('Y-m-d', strtotime("+$match days"));
            $waktu   = "16:00";
            $stmt = $conn->prepare("INSERT INTO jadwal_soccer (pertandingan, tanggal, waktu, tim1_rw, tim2_rw) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issii", $match, $tanggal, $waktu, $teams[$i], $teams[$j]);
            $stmt->execute();
            $match++;
        }
    }
}

// ====================
// Basketball → sistem knockout
// ====================
function regenerateBasket($conn) {
    $conn->query("TRUNCATE TABLE jadwal_basket");
    $teams = [];
    $res = $conn->query("SELECT rw FROM peserta_basket ORDER BY rw ASC");
    while ($row = $res->fetch_assoc()) $teams[] = $row['rw'];

    shuffle($teams);
    $match = 1;
    $round = 1;
    while (count($teams) > 1) {
        $next = [];
        for ($i = 0; $i < count($teams); $i += 2) {
            if (!isset($teams[$i+1])) break;
            $tanggal = date('Y-m-d', strtotime("+$match days"));
            $waktu   = "18:00";
            $stmt = $conn->prepare("INSERT INTO jadwal_basket (pertandingan, ronde, tim_a, tim_b, tanggal, waktu) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $match, $round, $teams[$i], $teams[$i+1], $tanggal, $waktu);
            $stmt->execute();
            $next[] = "Pemenang".$match;
            $match++;
        }
        $teams = $next;
        $round++;
    }
}

// ====================
// Volleyball → sistem liga
// ====================
function regenerateVolleyball($conn) {
    $conn->query("TRUNCATE TABLE jadwal_volleyball");
    $teams = [];
    $res = $conn->query("SELECT rw FROM peserta_volleyball ORDER BY rw ASC");
    while ($row = $res->fetch_assoc()) $teams[] = $row['rw'];

    $match = 1;
    $n = count($teams);
    for ($i = 0; $i < $n; $i++) {
        for ($j = $i + 1; $j < $n; $j++) {
            $tanggal = date('Y-m-d', strtotime("+$match days"));
            $waktu   = "19:00";
            $stmt = $conn->prepare("INSERT INTO jadwal_volleyball (pertandingan, tanggal, tim_a, tim_b, waktu) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $match, $tanggal, $teams[$i], $teams[$j], $waktu);
            $stmt->execute();
            $match++;
        }
    }
}

// ====================
// Chess → sistem swiss (sederhana)
// ====================
function regenerateChess($conn) {
    $conn->query("TRUNCATE TABLE jadwal_chess");
    $players = [];
    $res = $conn->query("SELECT rw FROM peserta_chess ORDER BY rw ASC");
    while ($row = $res->fetch_assoc()) $players[] = $row['rw'];

    $ronde = 1;
    $meja = 1;
    $n = count($players);
    for ($i = 0; $i < $n; $i += 2) {
        if (!isset($players[$i+1])) break;
        $waktu = "14:00";
        $stmt = $conn->prepare("INSERT INTO jadwal_chess (ronde, meja, pemain1, pemain2, waktu) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $ronde, $meja, $players[$i], $players[$i+1], $waktu);
        $stmt->execute();
        $meja++;
    }
}

// ====================
// Archery → sistem satu meja
// ====================
function regenerateArchery($conn) {
    $conn->query("TRUNCATE TABLE jadwal_archery");
    $players = [];
    $res = $conn->query("SELECT rw FROM peserta_archery ORDER BY rw ASC");
    while ($row = $res->fetch_assoc()) $players[] = $row['rw'];

    $p1 = $players[0] ?? null;
    $p2 = $players[1] ?? null;
    $p3 = $players[2] ?? null;
    $p4 = $players[3] ?? null;

    if ($p1 && $p2 && $p3 && $p4) {
        $waktu = "15:00";
        $stmt = $conn->prepare("INSERT INTO jadwal_archery (pertandingan, nama_pertandingan, peserta1, peserta2, peserta3, peserta4, waktu) VALUES (1, 'Sesi Utama', ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $p1, $p2, $p3, $p4, $waktu);
        $stmt->execute();
    }
}