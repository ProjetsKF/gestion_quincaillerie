<?php

require_once '../bd/database.php';
require_once '../log_activity.php';

session_start();

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

    /* enregistrer l'activité */

    logActivity(
        $pdo,
        $_SESSION['user_id'],
        "Suppression succursale refusée",
        "Tentative de suppression d'une succursale utilisée (ID : ".$idsuc.")"
    );

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

/* enregistrer l'activité */

logActivity(
    $pdo,
    $_SESSION['user_id'],
    "Suppression succursale",
    "Succursale supprimée (ID : ".$idsuc.")"
);

header("Location: index.php?success=deleted");
exit;