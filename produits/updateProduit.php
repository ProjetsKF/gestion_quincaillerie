<?php

require_once '../bd/database.php';
require_once '../log_activity.php';

session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $id     = $_POST['idprod'];
    $design = $_POST['designP'];
    $caract = $_POST['caractProduit'];

    $sql = "UPDATE produit
            SET designP = :design,
                caractProduit = :caract
            WHERE idprod = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':design' => $design,
        ':caract' => $caract,
        ':id'     => $id
    ]);

    /* enregistrer l'activité */

    logActivity(
        $pdo,
        $_SESSION['user_id'],
        "Modification produit",
        "Produit modifié : ".$design." (ID : ".$id.")"
    );

    header("Location: index.php?updated=1");
    exit;

}