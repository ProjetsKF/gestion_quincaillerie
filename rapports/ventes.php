<?php

require_once '../bd/database.php';
require_once '../auth_admin.php';

/* ===============================
   PAGINATION
================================= */

$limit = 10;

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;

/* ===============================
   RECHERCHE
================================= */

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

/* ===============================
   FILTRES
================================= */

$conditions = [];
$params = [];



/* MOIS */
if(!empty($_GET['mois'])){
    $conditions[] = "MONTH(co.datCom) = :mois";
    $params[':mois'] = $_GET['mois'];
}

/* ANNEE */
if(!empty($_GET['annee'])){
    $conditions[] = "YEAR(co.datCom) = :annee";
    $params[':annee'] = $_GET['annee'];
}

/* RECHERCHE */
if (!empty($search)) {
    $conditions[] = "(p.designP LIKE :search 
                    OR c.nom LIKE :search 
                    OR c.postnom LIKE :search 
                    OR co.idCom LIKE :search)";
    $params[':search'] = "%$search%";
}

/* CONSTRUIRE WHERE */
$where = "";

if(count($conditions) > 0){
    $where = " WHERE " . implode(" AND ", $conditions);
}

/* ===============================
   TOTAL DES VENTES (AVEC FILTRES)
================================= */

$sqlCount = "SELECT COUNT(*) 
             FROM detailscommande dc
             INNER JOIN commande co ON dc.idcom = co.idCom
             INNER JOIN client c ON co.idClt = c.idclt
             INNER JOIN produit p ON dc.idprod = p.idprod
             $where";

$stmtCount = $pdo->prepare($sqlCount);
$stmtCount->execute($params);

$totalRows = $stmtCount->fetchColumn();
$totalPages = ceil($totalRows / $limit);

/* ===============================
   REQUÊTE VENTES PAGINÉE
================================= */

$sql = "SELECT 
            dc.idcom,
            dc.idprod,
            dc.Qte,
            dc.unitMes,
            co.datCom AS date,
            c.nom,
            c.postnom,
            p.designP,
            pa.montant,
            pa.unitMon
        FROM detailscommande dc
        INNER JOIN commande co ON dc.idcom = co.idCom
        INNER JOIN client c ON co.idClt = c.idclt
        INNER JOIN produit p ON dc.idprod = p.idprod
        LEFT JOIN paiement pa ON pa.idCom = co.idCom
        $where
        ORDER BY dc.idcom DESC
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);

/* Lier paramètres filtres + recherche */
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

/* Pagination */
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

$ventes = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===============================
   STATISTIQUES
================================= */

$totalVentes = 0;
$totalQte = 0;

foreach ($ventes as $v) {
    $totalVentes += !empty($v['montant']) ? $v['montant'] : 0;
    $totalQte += $v['Qte'];
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>BISIKOMASH - Rapport ventes</title>

    <link rel="shortcut icon" href="/gestion_quincaillerie/img/icone.ico" type="image/x-icon">
    <link rel="icon" href="/gestion_quincaillerie/img/icone.ico" type="image/x-icon">

    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

<div id="wrapper">

    <?php include("../menu.php"); ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php include("../topbar.php"); ?>

            <div class="container-fluid">

                <!-- TITRE -->
                <h1 class="h3 mb-4 text-gray-800">
                    Rapport des ventes
                </h1>

               <!-- STATISTIQUES -->
<div class="row mb-4">

    <!-- TOTAL VENTES -->
    <div class="col-md-4">
        <div class="card shadow h-100 py-2 border-0"
             style="background: linear-gradient(45deg,#4facfe,#00f2fe); color:white;">
             
            <div class="card-body d-flex justify-content-between align-items-center">

                <div>
                    <div class="text-xs font-weight-bold">
                        Total des ventes
                    </div>

                    <div class="h5 font-weight-bold">
                        <?= number_format($totalVentes, 0, ",", " ") ?>
                    </div>
                </div>

                <i class="fas fa-coins fa-2x"></i>

            </div>
        </div>
    </div>

    <!-- QUANTITÉ VENDUE -->
    <div class="col-md-4">
        <div class="card shadow h-100 py-2 border-0"
             style="background: linear-gradient(45deg,#43e97b,#38f9d7); color:white;">

            <div class="card-body d-flex justify-content-between align-items-center">

                <div>
                    <div class="text-xs font-weight-bold">
                        Quantité vendue
                    </div>

                    <div class="h5 font-weight-bold">
                        <?= $totalQte ?>
                    </div>
                </div>

                <i class="fas fa-shopping-basket fa-2x"></i>

            </div>
        </div>
    </div>

    <!-- NOMBRE DE VENTES -->
    <div class="col-md-4">
        <div class="card shadow h-100 py-2 border-0"
             style="background: linear-gradient(45deg,#667eea,#764ba2); color:white;">

            <div class="card-body d-flex justify-content-between align-items-center">

                <div>
                    <div class="text-xs font-weight-bold">
                        Nombre de ventes
                    </div>

                    <div class="h5 font-weight-bold">
                        <?= count($ventes) ?>
                    </div>
                </div>

                <i class="fas fa-receipt fa-2x"></i>

            </div>
        </div>
    </div>

</div>

               <!-- TABLEAU -->
<div class="card shadow mb-4">

    <!-- HEADER -->
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            Liste des ventes
        </h6>

       <a href="#" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-file-pdf"></i> Exporter
        </a>
    </div>

    <div class="card-body">

        <!-- BARRE RECHERCHE -->
      <div class="d-flex flex-wrap align-items-center mb-3" style="gap:10px;">

    <!-- RAFRAICHIR -->
    <a href="ventes.php" class="btn btn-light btn-sm">
        <i class="fas fa-sync"></i>
    </a>

    <!-- RECHERCHE -->
    <form method="GET" class="d-flex align-items-center" style="gap:5px;">

        <input type="text" name="search"
               class="form-control form-control-sm"
               placeholder="Rechercher..."
               style="width:180px;">

        <button class="btn btn-primary btn-sm">
            <i class="fas fa-search"></i>
        </button>


        <!-- MOIS -->
        <select name="mois" class="form-control form-control-sm" style="width:100px;">
            <option value="">Mois</option>
            <?php for($m=1;$m<=12;$m++): ?>
                <option value="<?= $m ?>"><?= $m ?></option>
            <?php endfor; ?>
        </select>

        <!-- ANNEE -->
        <select name="annee" class="form-control form-control-sm" style="width:110px;">
            <option value="">Année</option>
            <?php for($y=2023;$y<=date('Y');$y++): ?>
                <option value="<?= $y ?>"><?= $y ?></option>
            <?php endfor; ?>
        </select>

        <!-- BOUTONS -->
        <button class="btn btn-primary btn-sm">
            Filtrer
        </button>

        <a href="ventes.php" class="btn btn-secondary btn-sm">
            Reset
        </a>

    </form>

</div>

        <!-- TABLE -->
        <div class="table-responsive">

            <table class="table table-hover table-striped table-sm">

                <thead class="thead-light">
                    <tr>
                        <th>Produit</th>
                        <th>Client</th>
                        <th>Quantité</th>
                        <th>Montant</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($ventes as $v): ?>

                    <tr>

                        <!-- PRODUIT -->
                        <td>
                            <i class="fas fa-box text-primary mr-2"></i>
                            <?= htmlspecialchars($v['designP']) ?>
                        </td>

                        <!-- CLIENT -->
                        <td>
                            <i class="fas fa-user text-muted mr-2"></i>
                            <?= htmlspecialchars($v['nom'] . " " . $v['postnom']) ?>
                        </td>

                        <!-- QUANTITÉ -->
                        <td>
                            <span class="badge badge-info">
                                <?= $v['Qte'] ?>
                            </span>
                        </td>

                        <!-- MONTANT -->
                        <td>
                            <?php if ($v['montant']): ?>
                                <span class="text-success font-weight-bold">
                                    <?= number_format($v['montant'], 0, ",", " ") ?>
                                    <?= $v['unitMon'] ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">Non payé</span>
                            <?php endif; ?>
                        </td>

                        <!-- DATE -->
                        <td>
                            <i class="fas fa-calendar-alt text-muted mr-1"></i>
                            <?php echo isset($v['date']) ? $v['date'] : '-'; ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<div class="d-flex justify-content-between align-items-center mt-3">

    <div class="small text-muted">
        Page <?= $page ?> sur <?= $totalPages ?>
    </div>

    <nav>
        <ul class="pagination pagination-sm mb-0">

            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">Précédent</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">Suivant</a>
                </li>
            <?php endif; ?>

        </ul>
    </nav>

</div>
            </div>

        </div>

        <?php include("../pieds.php"); ?>

    </div>

</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>

</body>

</html>