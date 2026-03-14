<?php

require_once '../bd/database.php';
require_once '../log_activity.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom       = trim($_POST['nom']);
    $postnom   = trim($_POST['postnom']);
    $pren      = trim($_POST['pren']);
    $denomSoc  = trim($_POST['denomSoc']);
    $tel       = trim($_POST['tel']);

    if ($nom && $tel) {

        $sql = "INSERT INTO fournisseur
                (nom, postnom, pren, denomSoc, tel)
                VALUES
                (:nom, :postnom, :pren, :denomSoc, :tel)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom'      => $nom,
            ':postnom'  => $postnom,
            ':pren'     => $pren,
            ':denomSoc' => $denomSoc,
            ':tel'      => $tel
        ]);

        /* enregistrer l'activité */

        logActivity(
            $pdo,
            $_SESSION['user_id'],
            "Création fournisseur",
            "Nouveau fournisseur ajouté : ".$nom." ".$postnom
        );

        // Redirection vers index avec message
        header("Location: index.php?success=1");
        exit;

    } else {

        header("Location: index.php?error=1");
        exit;
    }
}