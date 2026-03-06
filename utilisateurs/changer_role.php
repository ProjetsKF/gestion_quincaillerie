<?php

session_start();
require_once '../bd/database.php';
require_once '../auth_admin.php';

/* sécurité admin */

if($_SESSION['role'] != 1){
header("Location: ../dashboard.php");
exit;
}

$id = intval($_POST['id']);
$role = intval($_POST['role']);

$sql = "UPDATE utilisateur
        SET rol = :role
        WHERE idutil = :id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
':role'=>$role,
':id'=>$id
]);

header("Location: index.php");
exit;

?>