<?php

require_once '../bd/database.php';
require_once '../log_activity.php';

session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $id     = (int) $_POST['idprod'];
    $design = trim($_POST['designP']);
    $caract = trim($_POST['caractProduit']);
    $seuil  = isset($_POST['seuil_min']) ? (int) $_POST['seuil_min'] : 0;

    $sql = "UPDATE produit
            SET designP = :design,
                caractProduit = :caract,
                seuil_min = :seuil
            WHERE idprod = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':design' => $design,
        ':caract' => $caract,
        ':seuil'  => $seuil,
        ':id'     => $id
    ]);

    /* enregistrer l'activité */
    logActivity(
        $pdo,
        $_SESSION['user_id'],
        "Modification produit",
        "Produit modifié : ".$design." (ID : ".$id.", Seuil: ".$seuil.")"
    );

    header("Location: index.php?updated=1");
    exit;

}
?>