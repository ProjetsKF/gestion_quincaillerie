<?php

require_once '../bd/database.php';

$idAprov = $_POST['idAprov'];
$Qte = $_POST['Qte'];
$unitMes = $_POST['unitMes'];
$pu = $_POST['pu'];
$unitMon = $_POST['unitMon'];
$datAprov = $_POST['datAprov'];

$sql = "UPDATE approvisionnement
        SET Qte=?, unitMes=?, pu=?, unitMon=?, datAprov=?
        WHERE idAprov=?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$Qte,$unitMes,$pu,$unitMon,$datAprov,$idAprov]);

header("Location: stock.php?updated=1");

?>