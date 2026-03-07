<?php

require_once '../auth_admin.php';
require_once '../bd/database.php';

if(isset($_GET['id'])){

$id = intval($_GET['id']);

/* Empêcher l'utilisateur de se supprimer lui-même */

if($id == $_SESSION['user_id']){
header("Location: index.php?error=selfdelete");
exit;
}

/* Suppression */

$sql = "DELETE FROM utilisateur WHERE idutil = :id";
$stmt = $pdo->prepare($sql);

$stmt->execute([
':id' => $id
]);

}

header("Location: index.php");
exit;

?>