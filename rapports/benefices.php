<?php
session_start();
require_once '../bd/database.php';
require_once '../auth_admin.php';

/* ===============================
   VERIFICATION
================================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

/* ===============================
   INITIALISATION
================================= */
$where = array();
$params = array();
$whereSql = "";
$data = array();

/* ===============================
   FILTRES
================================= */
$date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : '';
$date_fin   = isset($_GET['date_fin']) ? $_GET['date_fin'] : '';
$produit    = isset($_GET['produit']) ? $_GET['produit'] : '';

if (!empty($date_debut) && !empty($date_fin)) {
    $where[] = "c.datCom BETWEEN :date_debut AND :date_fin";
    $params[':date_debut'] = $date_debut;
    $params[':date_fin']   = $date_fin;
}

if (!empty($produit)) {
    $where[] = "p.designP LIKE :produit";
    $params[':produit'] = "%$produit%";
}

if (!empty($where)) {
    $whereSql = "WHERE " . implode(" AND ", $where);
}

/* ===============================
   PAGINATION
================================= */
$limit = 25;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

/* ===============================
   TOTAL LIGNES
================================= */
$sqlCount = "
    SELECT COUNT(*) 
    FROM detailscommande dc
    JOIN commande c ON dc.idCom = c.idCom
    JOIN produit p ON dc.idprod = p.idprod
    $whereSql
";
$stmtCount = $pdo->prepare($sqlCount);
foreach ($params as $k => $v) {
    $stmtCount->bindValue($k, $v);
}
$stmtCount->execute();
$totalRows  = $stmtCount->fetchColumn();
$totalPages = ceil($totalRows / $limit);

/* ===============================
   REQUÊTE PRINCIPALE
================================= */
$sql = "
    SELECT 
        p.designP,
        dc.Qte,
        fp.unitMon,
        COALESCE(fp.pu, 0) AS prix_vente,
        COALESCE(a.pu, 0) AS prix_achat,
        (COALESCE(fp.pu, 0) - COALESCE(a.pu, 0)) * dc.Qte AS benefice,
        c.datCom AS dateCom
    FROM detailscommande dc
    JOIN commande c ON dc.idcom = c.idCom
    JOIN produit p ON dc.idprod = p.idprod

    LEFT JOIN (
        SELECT idProd, MAX(datAprov) AS lastDate
        FROM approvisionnement
        GROUP BY idProd
    ) lastA ON lastA.idProd = dc.idprod

    LEFT JOIN approvisionnement a 
        ON a.idProd = lastA.idProd 
        AND a.datAprov = lastA.lastDate

    LEFT JOIN fixationprix fp 
        ON fp.idApprov = a.idAprov

    $whereSql
    ORDER BY c.datCom DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===============================
   INDICATEURS – BÉNÉFICES
================================= */
$totalBeneficeUSD = 0;
$totalBeneficeCDF = 0;
$totalVentes = count($data);

foreach ($data as $d) {
    if ($d['unitMon'] === 'USD') {
        $totalBeneficeUSD += $d['benefice'];
    } elseif ($d['unitMon'] === 'CDF') {
        $totalBeneficeCDF += $d['benefice'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">
    <title>Rapport Bénéfices </title>

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

    <h1 class="h3 mb-4 text-gray-800">
        Rapport des bénéfices
    </h1>

    <!-- INDICATEURS -->
    <div class="row">

    <div class="col-md-6 mb-3">
    <div class="card text-white shadow"
         style="background: linear-gradient(45deg,#1cc88a,#17a673); border-radius:10px;">
        <div class="card-body">

            <div class="small">Bénéfice total</div>

            <!-- USD (principal) -->
            <h4 class="mb-1 font-weight-bold">
                <?= number_format($totalBeneficeUSD, 2, ',', ' ') ?> USD
            </h4>

            <!-- CDF (secondaire mais VISIBLE) -->
            <div class="mt-1 font-weight-semibold"
                 style="color: #eafff5; font-size: 16px;">
                <?= number_format($totalBeneficeCDF, 0, ',', ' ') ?> CDF
            </div>

        </div>
    </div>
</div>

        <div class="col-md-6 mb-3">
            <div class="card text-white shadow"
                 style="background: linear-gradient(45deg,#4e73df,#224abe); border-radius:10px;">
               <div class="card-body">

    <div class="small font-weight-bold">
        Nombre de ventes
    </div>

    <h4 class="mb-0 font-weight-bold">
        <?= isset($totalVentes) ? $totalVentes : 0; ?>
        <small style="font-size:14px;">ventes</small>
    </h4>

</div>
            </div>
        </div>

    </div>

    <!-- FILTRES -->
    <div class="card shadow mb-4 border-0">

        <div class="card-body">

            <form method="GET">

                <div class="row align-items-end">

                    <div class="col-md-3">
                        <label>Date début</label>
                        <input type="date" name="date_debut" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label>Date fin</label>
                        <input type="date" name="date_fin" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label>Produit</label>
                        <input type="text" name="produit"
                               class="form-control"
                               placeholder="Rechercher un produit...">
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-success btn-block">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>

                </div>

            </form>

        </div>

    </div>

    <!-- TABLE -->
    <div class="card shadow">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover">

                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Prix achat</th>
                            <th>Prix vente</th>
                            <th>Bénéfice</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (!empty($data)) { ?>

                            <?php $i = 1; foreach ($data as $d) { ?>

                                <tr>

                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $d['designP']; ?></td>
                                    <td><?php echo $d['Qte']; ?></td>
                                    <td>
                                        <?php echo number_format($d['prix_achat'], 2, ',', ' '); ?>
                                    </td>
                                    <td><?php echo $d['prix_vente']; ?></td>

                                    <td class="font-weight-bold text-success">

                                        <?php echo number_format($d['benefice'],0,',',' '); ?>
                                    </td>

                                    <td><?php echo $d['dateCom']; ?></td>

                                </tr>

                            <?php } ?>

                        <?php } else { ?>

                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    Aucun résultat
                                </td>
                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

            <!-- PAGINATION -->
            <?php
            $query = "&date_debut=$date_debut&date_fin=$date_fin&produit=$produit";
            ?>

            <ul class="pagination pagination-sm justify-content-center mt-3">

                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo ($page-1).$query; ?>">
                            Précédent
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i.$query; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo ($page+1).$query; ?>">
                            Suivant
                        </a>
                    </li>
                <?php endif; ?>

            </ul>

        </div>

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