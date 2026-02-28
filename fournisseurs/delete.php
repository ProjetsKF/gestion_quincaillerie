<?php
require_once '../bd/database.php';

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

    header("Location: index.php?error=used");
    exit;
}

/* ===============================
   2️⃣ Suppression
================================= */

$sql = "DELETE FROM fournisseur WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);

header("Location: index.php?delete=1");
exit;