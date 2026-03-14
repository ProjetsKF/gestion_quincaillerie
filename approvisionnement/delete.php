<?php

require_once '../bd/database.php';
require_once '../log_activity.php';

session_start();

if (isset($_GET['id'])) {

    $idAprov = (int) $_GET['id'];

    if ($idAprov > 0) {

        /* supprimer l'approvisionnement */

        $sql = "DELETE FROM approvisionnement 
                WHERE idAprov = :idAprov";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':idAprov' => $idAprov
        ]);

        /* enregistrer l'activité */

        logActivity(
            $pdo,
            $_SESSION['user_id'],
            "Suppression approvisionnement",
            "Suppression d'une entrée de stock (ID : ".$idAprov.")"
        );

        header("Location: stock.php?deleted=1");
        exit;
    }
}

header("Location: stock.php?error=1");
exit;

?>