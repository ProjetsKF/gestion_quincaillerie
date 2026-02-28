<?php
require_once '../bd/database.php';

if (isset($_GET['id'])) {

    $idAprov = (int) $_GET['id'];

    if ($idAprov > 0) {

        $sql = "DELETE FROM approvisionnement WHERE idAprov = :idAprov";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':idAprov' => $idAprov
        ));

        header("Location: index.php?deleted=1");
        exit;
    }
}

header("Location: index.php?error=1");
exit;
?>