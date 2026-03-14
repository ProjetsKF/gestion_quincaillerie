<?php

require_once '../bd/database.php';
require_once '../log_activity.php';

session_start();

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $designP = trim($_POST['designP']);
    $caractProduit = trim($_POST['caractProduit']);

    if ($designP && $caractProduit) {

        $sql = "INSERT INTO produit
                (designP, caractProduit)
                VALUES
                (:designP, :caractProduit)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':designP' => $designP,
            ':caractProduit' => $caractProduit
        ]);

        /* enregistrer l'activité */

        logActivity(
            $pdo,
            $_SESSION['user_id'],
            "Création produit",
            "Nouveau produit ajouté : ".$designP
        );

        header("Location: index.php?added=1");
        exit;

    } else {

        $message = "Tous les champs sont obligatoires.";
        $message_type = 'error';

    }
}
?>