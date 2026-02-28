<?php
require_once '../bd/database.php';

/* ===============================
   1️⃣ Vérification paramètre
================================= */

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$idsuc = (int) $_GET['id'];

if ($idsuc <= 0) {
    header("Location: index.php");
    exit;
}

/* ===============================
   2️⃣ Vérifier relation approvisionnement
================================= */

$check = $pdo->prepare("
    SELECT idAprov
    FROM approvisionnement
    WHERE idSuc = :idsuc
");

$check->execute([':idsuc' => $idsuc]);

if ($check->rowCount() > 0) {
    header("Location: index.php?error=used");
    exit;
}

/* ===============================
   3️⃣ Suppression réelle
================================= */

$delete = $pdo->prepare("
    DELETE FROM succursale
    WHERE idsuc = :idsuc
");

$delete->execute([':idsuc' => $idsuc]);

header("Location: index.php?success=deleted");
exit;