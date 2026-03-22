<?php

session_start();
require_once '../bd/database.php';
require_once '../auth_admin.php';

/* ===============================
   VÉRIFICATION CONNEXION
================================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

/* ===============================
   INITIALISATION
================================= */
$where   = array();
$params  = array();
$whereSql = "";
$achats  = array();

/* ===============================
   FILTRES
================================= */
$date_debut  = isset($_GET['date_debut']) ? $_GET['date_debut'] : '';
$date_fin    = isset($_GET['date_fin']) ? $_GET['date_fin'] : '';
$fournisseur = isset($_GET['fournisseur']) ? $_GET['fournisseur'] : '';
$succursale  = isset($_GET['succursale']) ? $_GET['succursale'] : '';

/* Filtre date */
if (!empty($date_debut) && !empty($date_fin)) {
    $where[] = "a.datAprov BETWEEN :date_debut AND :date_fin";
    $params[':date_debut'] = $date_debut;
    $params[':date_fin']   = $date_fin;
}

/* Filtre fournisseur */
if (!empty($fournisseur)) {
    $where[] = "a.idFourn = :fournisseur";
    $params[':fournisseur'] = $fournisseur;
}

/* Filtre succursale */
if (!empty($succursale)) {
    $where[] = "a.idSuc = :succursale";
    $params[':succursale'] = $succursale;
}

/* Construction WHERE */
if (count($where) > 0) {
    $whereSql = "WHERE " . implode(" AND ", $where);
}

/* ===============================
   PAGINATION
================================= */
$limit = 25;

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;

/* ===============================
   TOTAL ENREGISTREMENTS
================================= */
$sqlCount = "SELECT COUNT(*) 
             FROM approvisionnement a
             $whereSql";

$stmtCount = $pdo->prepare($sqlCount);
$stmtCount->execute($params);

$totalRows  = $stmtCount->fetchColumn();
$totalPages = ceil($totalRows / $limit);

/* ===============================
   REQUÊTE PRINCIPALE
================================= */
$sql = "SELECT 
            a.idAprov,
            p.designP,
            f.nom,
            f.postnom,
            a.Qte,
            a.pu,
            a.datAprov,
            s.nomSuc
        FROM approvisionnement a
        JOIN produit p ON a.idProd = p.idprod
        JOIN fournisseur f ON a.idFourn = f.id
        JOIN succursale s ON a.idSuc = s.idsuc
        $whereSql
        ORDER BY a.datAprov DESC
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);

/* Bind paramètres WHERE */
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

/* Bind pagination */
$stmt->bindValue(':limit',  (int)$limit,  PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

$stmt->execute();

/* Récupération données */
$achats = $stmt->fetchAll();

/* ===============================
   INDICATEURS
================================= */

/* Total achats */
$totalAchat = 0;

if (!empty($achats)) {
    foreach ($achats as $a) {
        $totalAchat += $a['Qte'] * $a['pu'];
    }
}

/* Nombre appro */
$nbAchat = count($achats);

/* Fournisseur principal (avec filtres) */
$sqlFourn = "SELECT f.nom, COUNT(*) as total
             FROM approvisionnement a
             JOIN fournisseur f ON a.idFourn = f.id
             $whereSql
             GROUP BY a.idFourn
             ORDER BY total DESC
             LIMIT 1";

$stmtFourn = $pdo->prepare($sqlFourn);

/* réutiliser les mêmes paramètres */
foreach ($params as $key => $value) {
    $stmtFourn->bindValue($key, $value);
}

$stmtFourn->execute();

$fournPrincipal = $stmtFourn->fetch();

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">
    <title>Rapport Achats</title>

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
                    Rapport des Achats
                </h1>

                <!-- ===============================
                     INDICATEURS
                ================================= -->
               <div class="row">

    <!-- TOTAL ACHATS -->
    <div class="col-md-4 mb-3">

        <div class="card text-white shadow" style="background: linear-gradient(45deg, #f6a23a, #f4b619); border-radius:10px;">

            <div class="card-body d-flex justify-content-between align-items-center">

                <div>
                    <div class="small">Total des achats</div>
                    <h4 class="mb-0">
                        <?php echo number_format($totalAchat, 0, ',', ' '); ?> 
                    </h4>
                </div>

                <i class="fas fa-shopping-cart fa-2x"></i>

            </div>

        </div>

    </div>

    <!-- NOMBRE APPRO -->
    <div class="col-md-4 mb-3">

        <div class="card text-white shadow" style="background: linear-gradient(45deg, #4e73df, #224abe); border-radius:10px;">

            <div class="card-body d-flex justify-content-between align-items-center">

                <div>
                    <div class="small">Nombre d'approvisionnements</div>
                    <h4 class="mb-0"><?php echo $nbAchat; ?></h4>
                </div>

                <i class="fas fa-clipboard-list fa-2x"></i>

            </div>

        </div>

    </div>

    <!-- FOURNISSEUR PRINCIPAL -->
    <div class="col-md-4 mb-3">

        <div class="card text-white shadow" style="background: linear-gradient(45deg, #1cc88a, #17a673); border-radius:10px;">

            <div class="card-body d-flex justify-content-between align-items-center">

                <div>
                    <div class="small">Fournisseur principal</div>

                    <h5 class="mb-0">
                        <?php 
                        echo (isset($fournPrincipal) && isset($fournPrincipal['nom'])) 
                            ? $fournPrincipal['nom'] 
                            : 'N/A'; 
                        ?>
                    </h5>
                </div>

                <i class="fas fa-truck fa-2x"></i>

            </div>

        </div>

    </div>

</div>

                <!-- ===============================
                     FILTRES
                ================================= -->
               <div class="card shadow mb-4 border-0">

    <div class="card-body">

        <form method="GET">

            <div class="row align-items-end">

                <!-- Date début -->
                <div class="col-md-3">
                    <label class="small text-muted">Date début</label>
                    <input type="date" name="date_debut" class="form-control">
                </div>

                <!-- Date fin -->
                <div class="col-md-3">
                    <label class="small text-muted">Date fin</label>
                    <input type="date" name="date_fin" class="form-control">
                </div>

                <!-- Fournisseur -->
                <div class="col-md-3">
                    <label class="small text-muted">Fournisseur</label>
                    <select name="fournisseur" class="form-control">

                        <option value="">Tous les fournisseurs</option>

                        <?php
                        $fournisseurs = $pdo->query("SELECT * FROM fournisseur");
                        while($f = $fournisseurs->fetch()){
                        ?>
                            <option value="<?php echo $f['id']; ?>">
                                <?php echo $f['nom'].' '.$f['postnom']; ?>
                            </option>
                        <?php } ?>

                    </select>
                </div>

                <!-- Succursale -->
                <div class="col-md-2">
                    <label class="small text-muted">Succursale</label>
                    <select name="succursale" class="form-control">

                        <option value="">Toutes</option>

                        <?php
                        $sucs = $pdo->query("SELECT * FROM succursale");
                        while($s = $sucs->fetch()){
                        ?>
                            <option value="<?php echo $s['idsuc']; ?>">
                                <?php echo $s['nomSuc']; ?>
                            </option>
                        <?php } ?>

                    </select>
                </div>

                <!-- Bouton -->
                <div class="col-md-1 text-right">
                    <button class="btn btn-success btn-block">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

            </div>

        </form>

    </div>

</div>

                <!-- ===============================
                     TABLEAU
                ================================= -->
                <div class="card shadow">

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-hover">

                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Produit</th>
                                        <th>Fournisseur</th>
                                        <th>Quantité</th>
                                        <th>Prix</th>
                                        <th>Total</th>
                                        <th>Date</th>
                                        <th>Succursale</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php if(count($achats) > 0){ ?>

                                        <?php $i=1; foreach($achats as $a){ ?>

                                            <tr>

                                                <td><?php echo $i++; ?></td>
                                                <td><?php echo $a['designP']; ?></td>
                                                <td><?php echo $a['nom']." ".$a['postnom']; ?></td>
                                                <td><?php echo $a['Qte']; ?></td>
                                                <td><?php echo $a['pu']; ?></td>
                                                <td><?php echo $a['Qte'] * $a['pu']; ?></td>
                                                <td><?php echo $a['datAprov']; ?></td>
                                                <td><?php echo $a['nomSuc']; ?></td>

                                            </tr>

                                        <?php } ?>

                                    <?php } else { ?>

                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                Aucun achat trouvé
                                            </td>
                                        </tr>

                                    <?php } ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

                <div class="d-flex justify-content-center mt-3">

                       <?php
$queryString = "&date_debut=$date_debut&date_fin=$date_fin&fournisseur=$fournisseur&succursale=$succursale";
?>

<ul class="pagination pagination-sm">

    <!-- Précédent -->
    <?php if ($page > 1): ?>
        <li class="page-item">
            <a class="page-link"
               href="?page=<?php echo ($page - 1) . $queryString; ?>">
                Précédent
            </a>
        </li>
    <?php endif; ?>

    <!-- Pages -->
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>

        <li class="page-item <?php if ($i == $page) echo 'active'; ?>">

            <a class="page-link"
               href="?page=<?php echo $i . $queryString; ?>">
                <?php echo $i; ?>
            </a>

        </li>

    <?php endfor; ?>

    <!-- Suivant -->
    <?php if ($page < $totalPages): ?>
        <li class="page-item">
            <a class="page-link"
               href="?page=<?php echo ($page + 1) . $queryString; ?>">
                Suivant
            </a>
        </li>
    <?php endif; ?>

</ul>

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