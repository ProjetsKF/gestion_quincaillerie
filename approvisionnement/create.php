<?php
require_once '../bd/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ===============================
    // 1️⃣ Nettoyage des données
    // ===============================

    $Qte      = isset($_POST['Qte']) ? (int) $_POST['Qte'] : 0;
    $unitMes  = isset($_POST['unitMes']) ? trim($_POST['unitMes']) : '';
    $pu       = isset($_POST['pu']) ? (float) $_POST['pu'] : 0;
    $idProd   = isset($_POST['idProd']) ? (int) $_POST['idProd'] : 0;
    $idFourn  = isset($_POST['idFourn']) ? (int) $_POST['idFourn'] : 0;
    $unitMon  = isset($_POST['unitMon']) ? trim($_POST['unitMon']) : '';
    $datAprov = isset($_POST['datAprov']) ? $_POST['datAprov'] : '';
    $idSuc    = isset($_POST['idSuc']) ? (int) $_POST['idSuc'] : 0;

    // ===============================
    // 2️⃣ Validation
    // ===============================

    if (
        $Qte > 0 &&
        !empty($unitMes) &&
        $pu > 0 &&
        $idProd > 0 &&
        $idFourn > 0 &&
        !empty($unitMon) &&
        !empty($datAprov) &&
        $idSuc > 0
    ) {

        $sql = "INSERT INTO approvisionnement
                (Qte, unitMes, pu, idProd, idFourn, unitMon, datAprov, idSuc)
                VALUES
                (:Qte, :unitMes, :pu, :idProd, :idFourn, :unitMon, :datAprov, :idSuc)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute(array(
            ':Qte'      => $Qte,
            ':unitMes'  => $unitMes,
            ':pu'       => $pu,
            ':idProd'   => $idProd,
            ':idFourn'  => $idFourn,
            ':unitMon'  => $unitMon,
            ':datAprov' => $datAprov,
            ':idSuc'    => $idSuc
        ));

        header("Location: index.php?success=1");
        exit;

    } else {

        header("Location: index.php?error=1");
        exit;
    }
}
?>