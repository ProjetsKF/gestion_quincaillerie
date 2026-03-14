<?php

require_once '../bd/database.php';
require_once '../log_activity.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nomSuc = $_POST['nomSuc'];
    $Quart  = $_POST['Quart'];
    $Comm   = $_POST['Comm'];
    $Aven   = $_POST['Aven'];

    $sql = "INSERT INTO succursale
            (nomSuc, Quart, Comm, Aven)
            VALUES (:nomSuc, :Quart, :Comm, :Aven)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':nomSuc' => $nomSuc,
        ':Quart'  => $Quart,
        ':Comm'   => $Comm,
        ':Aven'   => $Aven
    ]);

    /* enregistrer l'activité */

    logActivity(
        $pdo,
        $_SESSION['user_id'],
        "Création succursale",
        "Nouvelle succursale ajoutée : ".$nomSuc
    );

    header("Location: index.php?success=1");
    exit;
}