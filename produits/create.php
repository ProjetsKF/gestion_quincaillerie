<?php

require_once '../bd/database.php';
require_once '../log_activity.php';

session_start();

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $designP = trim($_POST['designP']);
    $caractProduit = trim($_POST['caractProduit']);
    $seuil_min = isset($_POST['seuil_min']) ? (int) $_POST['seuil_min'] : 0;

    // Validation
    if ($designP && $caractProduit && $seuil_min >= 0) {

        $sql = "INSERT INTO produit
                (designP, caractProduit, seuil_min)
                VALUES
                (:designP, :caractProduit, :seuil_min)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':designP' => $designP,
            ':caractProduit' => $caractProduit,
            ':seuil_min' => $seuil_min
        ]);

        /* enregistrer l'activité */
        logActivity(
            $pdo,
            $_SESSION['user_id'],
            "Création produit",
            "Nouveau produit ajouté : ".$designP." (Seuil min: ".$seuil_min.")"
        );

        header("Location: index.php?added=1");
        exit;

    } else {

        $message = "Tous les champs sont obligatoires et le seuil doit être valide.";
        $message_type = 'error';

    }
}
?>