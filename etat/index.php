<?php

session_start();
require_once '../bd/database.php';

/* ===============================
   VÉRIFICATION CONNEXION
================================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$role  = $_SESSION['role'];
$idsuc = $_SESSION['idsuc'];

/* ===============================
   RECHERCHE
================================= */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

/* ===============================
   PAGINATION
================================= */
$limit = 10;

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;

/* ===============================
   REQUÊTE PRINCIPALE
================================= */

$sql = "
SELECT * FROM (
    SELECT 
        p.idprod,
        p.designP,
        p.caractProduit,
        p.seuil_min,
        a.unitMes,
        f.pu,
        f.unitMon,
        COALESCE(a.totEntree,0) - COALESCE(c.totSortie,0) as stock,
        a.idAprov,
        a.idSuc,
        (SELECT nomSuc FROM succursale WHERE idsuc = a.idSuc) as succ
    FROM produit p
    LEFT JOIN (
        SELECT idprod, idAprov, unitMes, idSuc, SUM(Qte) as totEntree
        FROM approvisionnement
        GROUP BY idprod, idAprov
    ) a ON p.idprod = a.idProd
    LEFT JOIN (
        SELECT idprod, idApprov, SUM(Qte) as totSortie
        FROM detailscommande
        GROUP BY idprod, idApprov
    ) c ON a.idAprov = c.idApprov
    LEFT JOIN (
        SELECT idApprov, pu, unitMon
        FROM fixationprix
        GROUP BY idApprov
    ) f ON a.idAprov = f.idApprov
) rqt
";

/* ===============================
   FILTRAGE (RÔLE + RECHERCHE)
================================= */

$where = [];

if ($role != 1) {
    $where[] = "stock > 0";
    $where[] = "idAprov IN (SELECT idApprov FROM fixationPrix)";
    $where[] = "idSuc = :idsuc";
}

if (!empty($search)) {
    $where[] = "(designP LIKE :search OR caractProduit LIKE :search)";
}

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

/* ===============================
   TRI + PAGINATION
================================= */

$sql .= " ORDER BY idprod DESC LIMIT :limit OFFSET :offset";

/* ===============================
   EXÉCUTION
================================= */

$stmt = $pdo->prepare($sql);

// pagination
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

// rôle
if ($role != 1) {
    $stmt->bindValue(':idsuc', $idsuc, PDO::PARAM_INT);
}

// recherche
if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}

$stmt->execute();
$produits = $stmt->fetchAll();

/* ===============================
   TOTAL PRODUITS (PAGINATION)
================================= */

$countSql = "
SELECT COUNT(DISTINCT p.idprod)
FROM produit p
LEFT JOIN approvisionnement a ON p.idprod = a.idprod
";

$whereCount = [];

if ($role != 1) {
    $whereCount[] = "a.idSuc = :idsuc";
}

if (!empty($search)) {
    $whereCount[] = "(p.designP LIKE :search OR p.caractProduit LIKE :search)";
}

if (!empty($whereCount)) {
    $countSql .= " WHERE " . implode(" AND ", $whereCount);
}

$countQuery = $pdo->prepare($countSql);

// rôle
if ($role != 1) {
    $countQuery->bindValue(':idsuc', $idsuc, PDO::PARAM_INT);
}

// recherche
if (!empty($search)) {
    $countQuery->bindValue(':search', "%$search%", PDO::PARAM_STR);
}

$countQuery->execute();

$totalProducts = $countQuery->fetchColumn();
$totalPages = ceil($totalProducts / $limit);

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">
    <title>État du stock</title>

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

                <!-- Titre -->
                <h1 class="h3 mb-4 text-gray-800">
                    État du stock
                </h1>

                <!-- Card -->

                <div class="card shadow mb-4">

                  
                    <div class="card-body">

<div class="card-header py-3">

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">

        <!-- TITRE -->
        <h6 class="m-0 font-weight-bold text-primary mb-2 mb-md-0">
            Liste des produits en stock
        </h6>

        <!-- ACTIONS -->
        <div class="d-flex align-items-center">

            <!-- ACTUALISER -->
            <a href="index.php" class="btn btn-outline-secondary mr-2 shadow-sm">
                <i class="fas fa-sync-alt"></i>
            </a>

            <!-- RECHERCHE -->
            <form method="GET" class="mb-0">
                <div class="input-group" style="width: 250px;">

                    <input type="text" name="search"
                           class="form-control"
                           placeholder="Rechercher..."
                           value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">

                    <div class="input-group-append">
                        <button class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>

                </div>
            </form>

        </div>

    </div>

</div>
                        <div class="table-responsive">

                            <table class="table table-hover">

                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Désignation</th>
                                        <th>Caractéristiques</th>
                                        <th>Succursale</th>
                                        <th>Stock</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php if(count($produits) > 0){ ?>

                                        <?php $i = 1; foreach($produits as $p){ 

                                            $stock = $p['stock'];
                                            $seuil = $p['seuil_min'];

                                            if ($stock == 0) {
                                                $status = '<span class="badge badge-danger">Rupture</span>';
                                                $rowClass = 'table-danger';
                                            } elseif ($stock <= $seuil) {
                                                $status = '<span class="badge badge-warning">Faible</span>';
                                                $rowClass = 'table-warning';
                                            } else {
                                                $status = '<span class="badge badge-success">OK</span>';
                                                $rowClass = '';
                                            }

                                        ?>

                                        <tr class="<?php echo $rowClass; ?>">

                                            <td><?php echo $i++; ?></td>

                                            <td><?php echo htmlspecialchars($p['designP']); ?></td>

                                            <td><?php echo htmlspecialchars($p['caractProduit']); ?></td>
                                            <td><?php echo htmlspecialchars($p['succ']); ?></td>

                                            <td><?php echo $stock; ?></td>

                                            <td><?php echo $status; ?></td>

                                        </tr>

                                        <?php } ?>

                                    <?php } else { ?>

                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                Aucun produit trouvé
                                            </td>
                                        </tr>

                                    <?php } ?>

                                </tbody>

                            </table>

                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">

                            <ul class="pagination pagination-sm">

                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">
                                            Précédent
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">
                                            Suivant
                                        </a>
                                    </li>
                                <?php endif; ?>

                            </ul>

                        </div>

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