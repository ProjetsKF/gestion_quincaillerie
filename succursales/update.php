<?php

require_once '../bd/database.php';
require_once '../log_activity.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Vérification existence ID
    if (!isset($_POST['idsuc']) || empty($_POST['idsuc'])) {
        header("Location: index.php");
        exit;
    }

    $idsuc  = (int) $_POST['idsuc'];
    $nomSuc = trim($_POST['nomSuc']);
    $Quart  = trim($_POST['Quart']);
    $Comm   = trim($_POST['Comm']);
    $Aven   = trim($_POST['Aven']);

    // Vérification champ obligatoire
    if (empty($nomSuc)) {
        header("Location: index.php?error=champ_vide");
        exit;
    }

    try {

        $sql = "UPDATE succursale
                SET nomSuc = :nomSuc,
                    Quart  = :Quart,
                    Comm   = :Comm,
                    Aven   = :Aven
                WHERE idsuc = :idsuc";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':nomSuc' => $nomSuc,
            ':Quart'  => $Quart,
            ':Comm'   => $Comm,
            ':Aven'   => $Aven,
            ':idsuc'  => $idsuc
        ]);

        /* enregistrer l'activité */

        logActivity(
            $pdo,
            $_SESSION['user_id'],
            "Modification succursale",
            "Succursale modifiée : ".$nomSuc
        );

        header("Location: index.php?success=updated");
        exit;

    } catch (PDOException $e) {

        header("Location: index.php?error=sql_error");
        exit;
    }
}