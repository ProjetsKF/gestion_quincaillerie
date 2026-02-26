<?php
//session_start();
require_once '../bd/database.php';

/* Sécurité : recruteur uniquement 
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header('Location: ../login.php');
    exit;
}

*/

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $designP  = trim($_POST['designP']);
    $caractProduit = trim($_POST['caractProduit']);
    

    if ($designP && $caractProduit ) {

        $sql = "INSERT INTO produit
                (designP, caractProduit)
                VALUES
                (:designP, :caractProduit)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':designP'          => $designP,
            ':caractProduit'    => $caractProduit           
        ]);
        header('Location:index.php');

        $message = "Produit enregistré avec succès.";
        $message_type = 'success';

    } else {
        $message = "Tous les champs sont obligatoires.";
        $message_type = 'Erreur';
    }
}