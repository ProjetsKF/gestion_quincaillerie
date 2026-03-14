<?php

require_once '../bd/database.php';
require_once '../log_activity.php';

session_start();

$id = $_GET['id'];

$sql = "DELETE FROM produit WHERE idprod = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

/* enregistrer l'activité */

logActivity(
    $pdo,
    $_SESSION['user_id'],
    "Suppression produit",
    "Produit supprimé (ID : ".$id.")"
);

header("Location: index.php?deleted=1");
exit;

?>