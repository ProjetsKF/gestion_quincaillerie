<?php

session_start();

require_once '../bd/database.php';

/* Vérifier connexion */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

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
   TOTAL PRODUITS
================================= */

$countQuery = $pdo->query("SELECT COUNT(*) FROM produit");
$totalProducts = $countQuery->fetchColumn();

$totalPages = ceil($totalProducts / $limit);

/* ===============================
   REQUÊTE STOCK
================================= */

$sql = "SELECT 
            p.designP,
            p.caractProduit,
            p.seuil_min,
            COALESCE(a.totEntree,0) - COALESCE(c.totSortie,0) AS stock
        FROM produit p
        LEFT JOIN (
            SELECT idProd, SUM(Qte) as totEntree
            FROM approvisionnement
            GROUP BY idProd
        ) a ON p.idprod = a.idProd
        LEFT JOIN (
            SELECT idProd, SUM(Qte) as totSortie
            FROM detailscommande
            GROUP BY idProd
        ) c ON p.idprod = c.idProd
        ORDER BY p.idprod DESC
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$produits = $stmt->fetchAll();

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

                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Liste des produits en stock
                        </h6>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-hover">

                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Désignation</th>
                                        <th>Caractéristiques</th>
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