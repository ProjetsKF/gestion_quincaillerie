<?php

session_start();

require_once '../bd/database.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idAprov = isset($_POST['idAprov']) ? intval($_POST['idAprov']) : 0;
    $pu      = isset($_POST['pu']) ? floatval($_POST['pu']) : 0;
    $unitMon = isset($_POST['unitMon']) ? trim($_POST['unitMon']) : 'CDF';


    if ($idAprov > 0 && $pu > 0) {

        // Vérifier si un prix existe déjà
        $check = $pdo->prepare("
            SELECT idfix 
            FROM fixationprix
            WHERE IdApprov = :idAprov
        ");

        $check->execute([
            ':idAprov' => $idAprov
        ]);

        $existe = $check->fetch(PDO::FETCH_ASSOC);


        if ($existe) {

            // Mise à jour du prix
            $update = $pdo->prepare("
                UPDATE fixationprix
                SET pu = :pu,
                    unitMon = (Select unitMon from Approvisionnement Where idAprov=:idAprov)
                WHERE IdApprov = :idAprov
            ");

            $update->execute([
                ':pu'      => $pu,
                ':unitMon' => $unitMon,
                ':idAprov' => $idAprov
            ]);

            header("Location: index.php?success=updated");
            exit;

        } else {

            // Insertion nouveau prix
            $insert = $pdo->prepare("
                INSERT INTO fixationprix
                (pu, unitMon, IdApprov)
                VALUES
                (:pu, (Select unitMon from Approvisionnement Where idAprov=:idAprov), :idAprov)
            ");

            $insert->execute([
                ':pu'      => $pu,
                ':unitMon' => $unitMon,
                ':idAprov' => $idAprov
            ]);

            header("Location: index.php?success=created");
            exit;

        }

    } else {

        header("Location: index.php?error=invalid");
        exit;

    }

} else {

    header("Location: index.php");
    exit;

}

?>