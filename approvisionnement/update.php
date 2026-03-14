<?php

require_once '../bd/database.php';
require_once '../log_activity.php';

session_start();

$idAprov  = $_POST['idAprov'];
$Qte      = $_POST['Qte'];
$unitMes  = $_POST['unitMes'];
$pu       = $_POST['pu'];
$unitMon  = $_POST['unitMon'];
$datAprov = $_POST['datAprov'];

$sql = "UPDATE approvisionnement
        SET Qte = ?, unitMes = ?, pu = ?, unitMon = ?, datAprov = ?
        WHERE idAprov = ?";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $Qte,
    $unitMes,
    $pu,
    $unitMon,
    $datAprov,
    $idAprov
]);

/* enregistrer l'activité */

logActivity(
    $pdo,
    $_SESSION['user_id'],
    "Modification approvisionnement",
    "Modification d'une entrée de stock (ID : ".$idAprov.")"
);

header("Location: stock.php?updated=1");
exit;

?>