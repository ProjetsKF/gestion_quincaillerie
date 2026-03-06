<?php

require_once '../bd/database.php';

require_once '../auth_admin.php';

if(isset($_GET['id'])){

$id = intval($_GET['id']);

$sql = "UPDATE utilisateur
        SET statut='Actif'
        WHERE idutil=:id";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id'=>$id]);

header("Location: index.php");
exit;
}
?>