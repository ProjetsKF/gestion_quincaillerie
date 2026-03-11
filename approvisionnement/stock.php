<?php

session_start();
require_once '../bd/database.php';


/* ======================================
   PARAMETRES
====================================== */

$limit = 10;

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = ($page < 1) ? 1 : $page;

$start = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';



/* ======================================
   TOTAL DES ENREGISTREMENTS
====================================== */

if (!empty($search)) {

    $countSql = "
        SELECT COUNT(*)
        FROM approvisionnement a
        INNER JOIN produit p ON a.idProd = p.idprod
        INNER JOIN fournisseur f ON a.idFourn = f.id
        INNER JOIN succursale s ON a.idSuc = s.idsuc
        WHERE
            p.designP LIKE :search
            OR f.nom LIKE :search
            OR f.denomSoc LIKE :search
            OR s.nomSuc LIKE :search
    ";

    $stmtCount = $pdo->prepare($countSql);
    $stmtCount->execute([':search' => "%$search%"]);

} else {

    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM approvisionnement");
    $stmtCount->execute();

}

$totalRecords = $stmtCount->fetchColumn();
$totalPages   = ceil($totalRecords / $limit);



/* ======================================
   RECUPERATION DES DONNEES
====================================== */

$sql = "
    SELECT
        a.idAprov,
        a.Qte,
        a.unitMes,
        a.pu,
        a.unitMon,
        a.datAprov,

        p.designP,

        f.nom,
        f.postnom,
        f.pren,
        f.denomSoc,

        s.nomSuc

    FROM approvisionnement a

    INNER JOIN produit p
        ON a.idProd = p.idprod

    INNER JOIN fournisseur f
        ON a.idFourn = f.id

    INNER JOIN succursale s
        ON a.idSuc = s.idsuc
";

if (!empty($search)) {

    $sql .= "
        WHERE
            p.designP LIKE :search
            OR f.nom LIKE :search
            OR f.denomSoc LIKE :search
            OR s.nomSuc LIKE :search
    ";

}

$sql .= "
    ORDER BY a.idAprov DESC
    LIMIT :start, :limit
";

$stmt = $pdo->prepare($sql);

if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}

$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

$stmt->execute();

$approvisionnements = $stmt->fetchAll();


$showingFrom = ($totalRecords > 0) ? $start + 1 : 0;
$showingTo   = min($start + $limit, $totalRecords);

?>


<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">
    <title>BISIKOMASH - Stock</title>

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

                    <!-- MESSAGES DE CONFIRMATION -->

                    <?php if (isset($_GET['success'])): ?>

                    <div class="alert alert-success alert-dismissible fade show">
                        Approvisionnement ajouté avec succès.
                        <button type="button" class="close" data-dismiss="alert">
                            &times;
                        </button>
                    </div>

                    <?php endif; ?>


                    <?php if (isset($_GET['updated'])): ?>

                    <div class="alert alert-success alert-dismissible fade show">
                        Approvisionnement modifié avec succès.
                        <button type="button" class="close" data-dismiss="alert">
                            &times;
                        </button>
                    </div>

                    <?php endif; ?>


                    <?php if (isset($_GET['deleted'])): ?>

                    <div class="alert alert-success alert-dismissible fade show">
                        Approvisionnement supprimé avec succès.
                        <button type="button" class="close" data-dismiss="alert">
                            &times;
                        </button>
                    </div>

                    <?php endif; ?>


                    <?php if (isset($_GET['error'])): ?>

                    <div class="alert alert-danger alert-dismissible fade show">
                        Une erreur est survenue.
                        <button type="button" class="close" data-dismiss="alert">
                            &times;
                        </button>
                    </div>

                    <?php endif; ?>

                <!-- Barre de recherche -->

                <form method="GET">

                    <div class="input-group mb-4">

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Rechercher produit, fournisseur, succursale..."
                            value="<?php echo htmlspecialchars($search); ?>"
                        >

                        <div class="input-group-append">

                            <button class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>

                        </div>

                    </div>

                </form>

            <div class="card-header py-3">

            <div class="d-flex justify-content-between align-items-center">

                <h6 class="m-0 font-weight-bold text-primary">
                    Gestion des stocks
                </h6>

                <div class="btn-group">

                    <!-- Bouton Retour -->
                    <a href="/gestion_quincaillerie/approvisionnement/index.php"
                       class="btn btn-outline-primary btn-sm mr-2">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>

                    <!-- Bouton Actualiser -->
                    <a href="/gestion_quincaillerie/approvisionnement/stock.php"
                       class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-sync-alt"></i> Actualiser
                    </a>

                </div>

            </div>

        </div>

               <!-- TABLEAU -->

<div class="table-responsive">

    <table class="table table-hover table-sm">

        <thead class="thead-light">

            <tr>
                <th>ID</th>
                <th>Produit</th>
                <th>Fournisseur</th>
                <th>Succursale</th>
                <th>Quantité</th>
                <th>P.U</th>
                <th>Date</th>
                <th class="text-center">Actions</th>
            </tr>

        </thead>

        <tbody>

        <?php if (count($approvisionnements) > 0): ?>

            <?php foreach ($approvisionnements as $a): ?>

                <tr>

                    <td>
                        <?php echo $a['idAprov']; ?>
                    </td>

                    <td>
                        <i class="fas fa-box text-muted mr-1"></i>
                        <?php echo htmlspecialchars($a['designP']); ?>
                    </td>

                    <td>

                        <i class="fas fa-industry text-muted mr-1"></i>

                        <?php
                            echo htmlspecialchars(
                                $a['nom'] . ' ' .
                                $a['postnom'] . ' ' .
                                $a['pren']
                            );
                        ?>

                        <br>

                        <small class="text-muted">
                            <?php echo htmlspecialchars($a['denomSoc']); ?>
                        </small>

                    </td>

                    <td>
                        <i class="fas fa-building text-muted mr-1"></i>
                        <?php echo htmlspecialchars($a['nomSuc']); ?>
                    </td>

                    <td>
                        <?php echo $a['Qte']; ?>
                        <?php echo htmlspecialchars($a['unitMes']); ?>
                    </td>

                    <td>
                        <?php echo number_format($a['pu'], 2); ?>
                        <?php echo htmlspecialchars($a['unitMon']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($a['datAprov']); ?>
                    </td>

                    <td class="text-center">

                        <!-- Modifier -->
                        <a href="#"
                           class="text-primary btn-edit"
                           data-toggle="modal"
                           data-target="#editModal"

                           data-id="<?php echo $a['idAprov']; ?>"
                           data-qte="<?php echo $a['Qte']; ?>"
                           data-unitmes="<?php echo htmlspecialchars($a['unitMes']); ?>"
                           data-pu="<?php echo $a['pu']; ?>"
                           data-unitmon="<?php echo $a['unitMon']; ?>"
                           data-date="<?php echo $a['datAprov']; ?>"

                           title="Modifier">

                           <i class="fas fa-edit"></i>

                        </a>

                        <!-- Supprimer -->
                        <a href="delete.php?id=<?php echo $a['idAprov']; ?>"
                           class="text-danger"
                           title="Supprimer"
                           onclick="return confirm('Supprimer cet approvisionnement ?');">

                            <i class="fas fa-trash"></i>

                        </a>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>

                <td colspan="8" class="text-center text-muted">
                    Aucun approvisionnement enregistré
                </td>

            </tr>

        <?php endif; ?>

        </tbody>

    </table>

</div>



                <!-- PAGINATION -->

                <div class="d-flex justify-content-between align-items-center mt-3">

                    <div class="text-muted">

                        Affichage de
                        <?php echo $showingFrom; ?>
                        à
                        <?php echo $showingTo; ?>

                        sur
                        <?php echo $totalRecords; ?>
                        enregistrements

                    </div>


                    <nav>

                        <ul class="pagination pagination-sm">

                        <?php if ($page > 1): ?>

                            <li class="page-item">

                                <a
                                    class="page-link"
                                    href="?page=<?php echo $page - 1 ?>&search=<?php echo urlencode($search) ?>"
                                >
                                    Previous
                                </a>

                            </li>

                        <?php endif; ?>


                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">

                                <a
                                    class="page-link"
                                    href="?page=<?php echo $i ?>&search=<?php echo urlencode($search) ?>"
                                >
                                    <?php echo $i ?>
                                </a>

                            </li>

                        <?php endfor; ?>


                        <?php if ($page < $totalPages): ?>

                            <li class="page-item">

                                <a
                                    class="page-link"
                                    href="?page=<?php echo $page + 1 ?>&search=<?php echo urlencode($search) ?>"
                                >
                                    Next
                                </a>

                            </li>

                        <?php endif; ?>

                        </ul>

                    </nav>

                </div>


            </div>


            <?php include("../pieds.php"); ?>


        </div>

    </div>

</div>


<div class="modal fade" id="editModal">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Modifier Approvisionnement
                </h5>

                <button class="close" data-dismiss="modal">
                    &times;
                </button>

            </div>

            <form method="POST" action="update.php">

                <input type="hidden" name="idAprov" id="edit_id">

                <div class="modal-body">

                    <div class="form-group">
                        <label>Quantité</label>
                        <input type="number"
                               name="Qte"
                               id="edit_qte"
                               class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Unité de mesure</label>
                        <input type="text"
                               name="unitMes"
                               id="edit_unitmes"
                               class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Prix unitaire</label>
                        <input type="number"
                               step="0.01"
                               name="pu"
                               id="edit_pu"
                               class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Devise</label>

                        <select name="unitMon"
                                id="edit_unitmon"
                                class="form-control">

                            <option value="USD">USD</option>
                            <option value="CDF">CDF</option>
                            <option value="EUR">EUR</option>

                        </select>

                    </div>

                    <div class="form-group">
                        <label>Date</label>
                        <input type="date"
                               name="datAprov"
                               id="edit_date"
                               class="form-control"
                               required>
                    </div>

                </div>

                <div class="modal-footer">

                    <button class="btn btn-success">
                        Mettre à jour
                    </button>

                    <button class="btn btn-secondary"
                            data-dismiss="modal">
                        Annuler
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>

<script>

document.querySelectorAll(".btn-edit").forEach(button => {

    button.addEventListener("click", function () {

        document.getElementById("edit_id").value = this.dataset.id;
        document.getElementById("edit_qte").value = this.dataset.qte;
        document.getElementById("edit_unitmes").value = this.dataset.unitmes;
        document.getElementById("edit_pu").value = this.dataset.pu;
        document.getElementById("edit_unitmon").value = this.dataset.unitmon;
        document.getElementById("edit_date").value = this.dataset.date;

    });

});

</script>

</body>
</html>