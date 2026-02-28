<?php
require_once '../bd/database.php';

$sql = "SELECT 
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

        INNER JOIN produit p ON a.idProd = p.idprod
        INNER JOIN fournisseur f ON a.idFourn = f.id
        INNER JOIN succursale s ON a.idSuc = s.idsuc

        ORDER BY a.idAprov DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$approvisionnements = $stmt->fetchAll();

// Recuperation des Produits
$prodStmt = $pdo->prepare("SELECT idprod, designP FROM produit ORDER BY designP ASC");
$prodStmt->execute();
$produits = $prodStmt->fetchAll();

// Recuperation des Fournisseurs
$fournStmt = $pdo->prepare("SELECT id, nom, postnom FROM fournisseur ORDER BY nom ASC");
$fournStmt->execute();
$fournisseurs = $fournStmt->fetchAll();

// Recuperation des Succursales
$sucStmt = $pdo->prepare("SELECT idsuc, nomSuc FROM succursale ORDER BY nomSuc ASC");
$sucStmt->execute();
$succursales = $sucStmt->fetchAll();

// pour la recherche


$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {

    $sql = "SELECT 
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

            INNER JOIN produit p ON a.idProd = p.idprod
            INNER JOIN fournisseur f ON a.idFourn = f.id
            INNER JOIN succursale s ON a.idSuc = s.idsuc

            WHERE 
                p.designP LIKE :search
                OR f.nom LIKE :search
                OR f.postnom LIKE :search
                OR f.pren LIKE :search
                OR f.denomSoc LIKE :search
                OR s.nomSuc LIKE :search
                OR a.unitMon LIKE :search
                OR a.datAprov LIKE :search

            ORDER BY a.idAprov DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':search' => "%$search%"
    ));

} else {

    $sql = "SELECT 
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
            INNER JOIN produit p ON a.idProd = p.idprod
            INNER JOIN fournisseur f ON a.idFourn = f.id
            INNER JOIN succursale s ON a.idSuc = s.idsuc
            ORDER BY a.idAprov DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}

$approvisionnements = $stmt->fetchAll();



// PAGINATION


$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = $page < 1 ? 1 : $page;
$start = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

/* ===== TOTAL ===== */

if (!empty($search)) {

    $countSql = "SELECT COUNT(*)
                 FROM approvisionnement a
                 INNER JOIN produit p ON a.idProd = p.idprod
                 INNER JOIN fournisseur f ON a.idFourn = f.id
                 INNER JOIN succursale s ON a.idSuc = s.idsuc
                 WHERE p.designP LIKE :search
                    OR f.nom LIKE :search
                    OR f.denomSoc LIKE :search
                    OR s.nomSuc LIKE :search";

    $stmtCount = $pdo->prepare($countSql);
    $stmtCount->execute([':search' => "%$search%"]);

} else {

    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM approvisionnement");
    $stmtCount->execute();
}

$totalRecords = $stmtCount->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

/* ===== DONNÉES ===== */

$sql = "SELECT 
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
        INNER JOIN produit p ON a.idProd = p.idprod
        INNER JOIN fournisseur f ON a.idFourn = f.id
        INNER JOIN succursale s ON a.idSuc = s.idsuc";

if (!empty($search)) {
    $sql .= " WHERE p.designP LIKE :search
              OR f.nom LIKE :search
              OR f.denomSoc LIKE :search
              OR s.nomSuc LIKE :search";
}

$sql .= " ORDER BY a.idAprov DESC
          LIMIT :start, :limit";

$stmt = $pdo->prepare($sql);

if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}

$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();

$approvisionnements = $stmt->fetchAll();

$showingFrom = $totalRecords > 0 ? $start + 1 : 0;
$showingTo = min($start + $limit, $totalRecords);


?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>BISIKOMASH - Stock</title>

    <!-- Custom fonts for this template-->
<link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
   <link href="../css/sb-admin-2.min.css" rel="stylesheet">

   <style>
    /* Tableau approvisionnement compact */

    .custom-table thead th {
        font-size: 0.85rem;
        padding: 8px 10px;
    }

    .custom-table tbody td {
        font-size: 0.82rem;
        padding: 6px 10px;
    }

    .custom-table i {
        font-size: 0.80rem;
    }
</style>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

         <!-- MENU -->

                    <?php include("../menu.php"); ?>

        <!-- FIN MENU -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

               
            <!-- Topbar -->

                 <?php include("../topbar.php"); ?>

            <!-- End of Topbar -->

<!-- Begin Page Content -->
<div class="container-fluid">

<!-- LES MESSAGES -->
    <?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">
        Approvisionnement modifié avec succès.
    </div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success">
        Approvisionnement supprimé avec succès.
    </div>
    <?php endif; ?>

    <!-- Retour -->
    <div class="mb-3">
        <a href="/gestion_quincaillerie/dashboard.php" class="text-secondary">
            <i class="fas fa-arrow-left"></i> Retour au tableau de bord
        </a>
    </div>

    <!-- Card principale -->
    <div class="card shadow mb-4">

        <!-- Header -->
        <div class="card-header py-3">
    <div class="d-flex justify-content-between align-items-center">

        <h6 class="m-0 font-weight-bold text-primary">
            Gestion des stocks
        </h6>

        <div class="btn-group">

            <!-- Bouton Actualiser -->
            <a href="index.php" class="btn btn-outline-secondary btn-sm mr-2">
                <i class="fas fa-sync-alt"></i> Actualiser
            </a>

            <!-- Bouton Nouvelle Succursale -->
           <button class="btn btn-primary btn-sm"
                data-toggle="modal"
                data-target="#approModal">
            <i class="fas fa-plus"></i> Nouveau Stock
        </button>

        </div>

    </div>
</div>

        <!-- Body -->
        <div class="card-body">

          <!-- Barre de recherche -->
<div class="mb-4">
    <form method="GET" action="index.php">
        <div class="input-group">

            <input type="text"
               name="search"
               class="form-control"
               placeholder="Rechercher par produit, fournisseur, succursale, date ou devise..."
               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">

            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>

        </div>
    </form>
</div>

            <!-- Tableau -->
            <div class="table-responsive">
                <table class="table table-hover align-items-center table-sm custom-table">
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

    <td><?php echo $a['idAprov']; ?></td>

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
        <?php echo $a['Qte']; ?> <?php echo htmlspecialchars($a['unitMes']); ?>
    </td>

    <td>
        <?php echo number_format($a['pu'], 2); ?> 
        <?php echo htmlspecialchars($a['unitMon']); ?>
    </td>

    <td>
        <?php echo htmlspecialchars($a['datAprov']); ?>
    </td>

    <td class="text-center">

        <a href="#"
           data-toggle="modal"
           data-target="#editModal<?php echo $a['idAprov']; ?>"
           class="text-primary mr-3">
            <i class="fas fa-edit"></i>
        </a>

        <a href="delete.php?id=<?php echo $a['idAprov']; ?>"
           class="text-danger"
           onclick="return confirm('Supprimer cet approvisionnement ?');">
            <i class="fas fa-trash"></i>
        </a>

    </td>

</tr>
<!-- Modal Modifier Approvisionnement -->
<div class="modal fade"
     id="editModal<?php echo $a['idAprov']; ?>"
     tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg rounded">

            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-edit text-primary"></i>
                    Modifier Approvisionnement
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form method="POST" action="update.php">

                <input type="hidden"
                       name="idAprov"
                       value="<?php echo $a['idAprov']; ?>">

                <div class="modal-body">

                    <div class="form-group">
                        <label>Quantité *</label>
                        <input type="number"
                               name="Qte"
                               class="form-control"
                               value="<?php echo $a['Qte']; ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Unité de mesure *</label>
                        <input type="text"
                               name="unitMes"
                               class="form-control"
                               value="<?php echo htmlspecialchars($a['unitMes']); ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Prix unitaire *</label>
                        <input type="number"
                               step="0.01"
                               name="pu"
                               class="form-control"
                               value="<?php echo $a['pu']; ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Devise *</label>
                        <select name="unitMon" class="form-control" required>
                            <option value="USD" <?php if($a['unitMon']=='USD') echo 'selected'; ?>>USD</option>
                            <option value="CDF" <?php if($a['unitMon']=='CDF') echo 'selected'; ?>>CDF</option>
                            <option value="EUR" <?php if($a['unitMon']=='EUR') echo 'selected'; ?>>EUR</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Date *</label>
                        <input type="date"
                               name="datAprov"
                               class="form-control"
                               value="<?php echo $a['datAprov']; ?>"
                               required>
                    </div>

                </div>

                <div class="modal-footer border-0">

                    <button type="submit"
                            class="btn btn-success">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>

                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        Annuler
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<?php endforeach; ?>

<?php else: ?>

<tr>
    <td colspan="8" class="text-center text-muted">
        Aucun approvisionnement enregistré.
    </td>
</tr>

<?php endif; ?>

</tbody>
                </table>

                <div class="d-flex justify-content-between align-items-center mt-3">

                <div class="text-muted" style="font-size:0.9rem;">
                    Affichage de <?php echo $showingFrom; ?>
                    à <?php echo $showingTo; ?>
                    sur <?php echo $totalRecords; ?> approvisionnement(s)

                     <nav>
        <ul class="pagination pagination-sm mb-0">

            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link"
                       href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">
                        Previous
                    </a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                    <a class="page-link"
                       href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link"
                       href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">
                        Next
                    </a>
                </li>
            <?php endif; ?>

        </ul>
    </nav>
                </div>
            </div>

        </div>
    </div>

</div>
<!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

             <!-- PIED DE PAGE -->

                     <?php include("../pieds.php"); ?>

             <!-- FIN PIED DE PAGE -->


        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>



<!-- Modal Ajout Approvisionnement -->
<div class="modal fade" id="approModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg rounded">

            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-truck-loading text-primary"></i>
                    Nouvel Approvisionnement
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form method="POST" action="create.php">

                <div class="modal-body">

                    <!-- Produit -->
                    <div class="form-group">
                        <label>Produit *</label>
                        <select name="idProd" class="form-control" required>
                            <option value="">-- Sélectionner un produit --</option>
                            <?php foreach ($produits as $p): ?>
                                <option value="<?php echo $p['idprod']; ?>">
                                    <?php echo htmlspecialchars($p['designP']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Fournisseur -->
                    <div class="form-group">
                        <label>Fournisseur *</label>
                        <select name="idFourn" class="form-control" required>
                            <option value="">-- Sélectionner un fournisseur --</option>
                            <?php foreach ($fournisseurs as $f): ?>
                                <option value="<?php echo $f['id']; ?>">
                                    <?php echo htmlspecialchars($f['nom'] . ' ' . $f['postnom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Succursale -->
                    <div class="form-group">
                        <label>Succursale *</label>
                        <select name="idSuc" class="form-control" required>
                            <option value="">-- Sélectionner une succursale --</option>
                            <?php foreach ($succursales as $s): ?>
                                <option value="<?php echo $s['idsuc']; ?>">
                                    <?php echo htmlspecialchars($s['nomSuc']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Quantité -->
                    <div class="form-group">
                        <label>Quantité *</label>
                        <input type="number" name="Qte" class="form-control" required>
                    </div>

                    <!-- Unité de mesure -->
                    <div class="form-group">
                        <label>Unité de mesure *</label>
                        <input type="text" name="unitMes" class="form-control" placeholder="Ex: Carton, Sac, Pièce" required>
                    </div>

                    <!-- Prix Unitaire -->
                    <div class="form-group">
                        <label>Prix unitaire *</label>
                        <input type="number" step="0.01" name="pu" class="form-control" required>
                    </div>

                    <!-- Devise -->
                    <div class="form-group">
                        <label>Devise *</label>
                        <select name="unitMon" class="form-control" required>
                            <option value="USD">USD</option>
                            <option value="CDF">CDF</option>
                            <option value="EUR">EUR</option>
                        </select>
                    </div>

                    <!-- Date -->
                    <div class="form-group">
                        <label>Date *</label>
                        <input type="date" name="datAprov" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer border-0">

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>

                    <button type="button"
                            class="btn btn-secondary"
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
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>

</body>

</html>