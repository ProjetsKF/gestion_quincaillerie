<?php

require_once '../bd/database.php';
require_once '../log_activity.php';

session_start();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);

if (!$id) {
    header("Location: index.php");
    exit;
}

/* ===============================
   1️⃣ Vérifier si fournisseur utilisé
================================= */

$check = $pdo->prepare("
    SELECT idAprov 
    FROM approvisionnement 
    WHERE idFourn = :id
");

$check->execute([':id' => $id]);

if ($check->rowCount() > 0) {

    /* enregistrer l'activité */

    logActivity(
        $pdo,
        $_SESSION['user_id'],
        "Suppression fournisseur refusée",
        "Tentative de suppression d'un fournisseur utilisé (ID : ".$id.")"
    );

    header("Location: index.php?error=used");
    exit;
}

/* ===============================
   2️⃣ Suppression
================================= */

$sql = "DELETE FROM fournisseur WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);

/* enregistrer l'activité */

logActivity(
    $pdo,
    $_SESSION['user_id'],
    "Suppression fournisseur",
    "Fournisseur supprimé (ID : ".$id.")"
);

header("Location: index.php?delete=1");
exit;