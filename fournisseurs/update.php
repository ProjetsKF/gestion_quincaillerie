<?php

require_once '../bd/database.php';
require_once '../log_activity.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id        = intval($_POST['id']);
    $nom       = trim($_POST['nom']);
    $postnom   = trim($_POST['postnom']);
    $pren      = trim($_POST['pren']);
    $denomSoc  = trim($_POST['denomSoc']);
    $tel       = trim($_POST['tel']);

    if ($id && $nom && $tel) {

        $sql = "UPDATE fournisseur SET
                nom = :nom,
                postnom = :postnom,
                pren = :pren,
                denomSoc = :denomSoc,
                tel = :tel
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':nom'      => $nom,
            ':postnom'  => $postnom,
            ':pren'     => $pren,
            ':denomSoc' => $denomSoc,
            ':tel'      => $tel,
            ':id'       => $id
        ]);

        /* enregistrer l'activité */

        logActivity(
            $pdo,
            $_SESSION['user_id'],
            "Modification fournisseur",
            "Fournisseur modifié : ".$nom." ".$postnom
        );

        header("Location: index.php?update=1");
        exit;
    }
}