<?php

require_once '../bd/database.php';

$search = isset($_GET['txtRech']) ? trim($_GET['txtRech']) : '';

$sql = "

SELECT DISTINCT
    c.idCom,
    c.datCom,

    cl.nom,
    cl.postnom,
    cl.prenom,
    cl.raisSoc,
    cl.tel,

    s.nomSuc,
    s.Comm

FROM commande c

INNER JOIN client cl 
    ON c.idClt = cl.idclt

INNER JOIN succursale s 
    ON c.idSuc = s.idsuc

WHERE

cl.nom LIKE :search
OR cl.postnom LIKE :search
OR cl.prenom LIKE :search
OR cl.tel LIKE :search
OR c.datCom LIKE :search

ORDER BY c.idCom DESC

";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':search' => "%$search%"
]);

$resultats = $stmt->fetchAll();

?>