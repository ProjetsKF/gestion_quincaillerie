<?php
require_once '../bd/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idAprov  = isset($_POST['idAprov']) ? (int) $_POST['idAprov'] : 0;
    $Qte      = isset($_POST['Qte']) ? (int) $_POST['Qte'] : 0;
    $unitMes  = isset($_POST['unitMes']) ? trim($_POST['unitMes']) : '';
    $pu       = isset($_POST['pu']) ? (float) $_POST['pu'] : 0;
    $unitMon  = isset($_POST['unitMon']) ? trim($_POST['unitMon']) : '';
    $datAprov = isset($_POST['datAprov']) ? $_POST['datAprov'] : '';

    if ($idAprov > 0 && $Qte > 0 && $pu > 0) {

        $sql = "UPDATE approvisionnement
                SET Qte = :Qte,
                    unitMes = :unitMes,
                    pu = :pu,
                    unitMon = :unitMon,
                    datAprov = :datAprov
                WHERE idAprov = :idAprov";

        $stmt = $pdo->prepare($sql);

        $stmt->execute(array(
            ':Qte'      => $Qte,
            ':unitMes'  => $unitMes,
            ':pu'       => $pu,
            ':unitMon'  => $unitMon,
            ':datAprov' => $datAprov,
            ':idAprov'  => $idAprov
        ));

        header("Location: index.php?updated=1");
        exit;
    }

    header("Location: index.php?error=1");
    exit;
}
?>