<?php

session_start();

require_once("../bd/database.php");

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $nom    = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email  = $_POST['email'];

    $id = $_SESSION['user_id'];

    $sql = "UPDATE utilisateur
            SET nom = ?, prenom = ?, email = ?
            WHERE idutil = ?";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $nom,
        $prenom,
        $email,
        $id
    ]);

    header("Location: ../profile.php");
    exit();
}