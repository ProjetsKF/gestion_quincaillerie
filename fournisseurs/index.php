<?php
require_once '../bd/database.php';

/* ===============================
   PARAMÈTRES PAGINATION
================================= */

$limit = 5; // Nombre d’éléments par page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = $page < 1 ? 1 : $page;
$start = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

/* ===============================
   COMPTER LE TOTAL
================================= */

if (!empty($search)) {

    $countSql = "SELECT COUNT(*) FROM fournisseur
                 WHERE nom LIKE :search
                 OR postnom LIKE :search
                 OR pren LIKE :search
                 OR denomSoc LIKE :search
                 OR tel LIKE :search";

    $stmtCount = $pdo->prepare($countSql);
    $stmtCount->execute([':search' => "%$search%"]);

} else {

    $countSql = "SELECT COUNT(*) FROM fournisseur";
    $stmtCount = $pdo->prepare($countSql);
    $stmtCount->execute();
}

$totalRecords = $stmtCount->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

/* ===============================
   RÉCUPÉRER DONNÉES PAGINÉES
================================= */

if (!empty($search)) {

    $sql = "SELECT * FROM fournisseur
            WHERE nom LIKE :search
            OR postnom LIKE :search
            OR pren LIKE :search
            OR denomSoc LIKE :search
            OR tel LIKE :search
            ORDER BY id DESC
            LIMIT :start, :limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

} else {

    $sql = "SELECT * FROM fournisseur
            ORDER BY id DESC
            LIMIT :start, :limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
}

$fournisseurs = $stmt->fetchAll();

/* Calcul affichage */
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

    <title>BISIKOMASH - Fournisseurs</title>

    <!-- Custom fonts for this template-->
<link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
   <link href="../css/sb-admin-2.min.css" rel="stylesheet">

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

    <!-- MESSAGES -->

    <?php if (isset($_GET['delete'])): ?>
        <div class="alert alert-success">
            Fournisseur supprimé avec succès.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] == 'used'): ?>
        <div class="alert alert-danger">
            Impossible de supprimer : fournisseur déjà utilisé.
        </div>
    <?php endif; ?>


    <!-- Retour -->
    <div class="mb-3">
        <a href="../dashboard.php" class="text-secondary">
            <i class="fas fa-arrow-left"></i> Retour au tableau de bord
        </a>
    </div>

    <!-- Card principale -->
    <div class="card shadow mb-4">

        <!-- Header -->
       <div class="card-header py-3 d-flex justify-content-between align-items-center">

    <h6 class="m-0 font-weight-bold text-primary">
        Gestion des Fournisseurs
    </h6>

    <div>

        <!-- Bouton Actualiser -->
        <a href="index.php" class="btn btn-secondary btn-sm mr-2">
            <i class="fas fa-sync-alt"></i> Actualiser
        </a>

        <!-- Bouton Nouveau Fournisseur -->
        <button class="btn btn-primary btn-sm"
                data-toggle="modal"
                data-target="#fournisseurModal">
            <i class="fas fa-plus"></i> Nouveau Fournisseur
        </button>

    </div>

</div>

        <!-- Body -->
        <div class="card-body">

           <!-- Barre de recherche -->
<div class="mb-4">
    <form method="GET">
        <div class="input-group">
            <input type="text"
                   name="search"
                   class="form-control"
                   placeholder="Rechercher par nom, société ou téléphone..."
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
                <table class="table table-hover align-items-center">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Nom Complet</th>
                            <th>Société</th>
                            <th>Téléphone</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

<?php if (count($fournisseurs) > 0): ?>

    <?php foreach ($fournisseurs as $f): ?>
        <tr>
            <td><?php echo $f['id']; ?></td>

            <td>
                <i class="fas fa-user mr-1 text-muted"></i>
                <?php 
                echo htmlspecialchars(
                    $f['nom'] . ' ' . 
                    $f['postnom'] . ' ' . 
                    $f['pren']
                ); 
                ?>
            </td>

            <td>
                <i class="fas fa-building mr-1 text-muted"></i>
                <?php echo htmlspecialchars($f['denomSoc']); ?>
            </td>

            <td>
                <i class="fas fa-phone mr-1 text-muted"></i>
                <?php echo htmlspecialchars($f['tel']); ?>
            </td>

            <td class="text-center">
                <a href="#" 
                   class="text-primary mr-3"
                   data-toggle="modal"
                   data-target="#editModal<?php echo $f['id']; ?>">
                    <i class="fas fa-edit"></i>
                </a>

                <a href="delete.php?id=<?php echo $f['id']; ?>"
                   class="text-danger"
                   onclick="return confirm('Voulez-vous vraiment supprimer ce fournisseur ?');">
                    <i class="fas fa-trash"></i>
                </a>
            </td>
        </tr>

        <!-- Modal Modifier Fournisseur -->
<div class="modal fade" id="editModal<?php echo $f['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg rounded">

            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-edit text-primary"></i>
                    Modifier Fournisseur
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form method="POST" action="update.php">

                <input type="hidden" name="id" value="<?php echo $f['id']; ?>">

                <div class="modal-body">

                    <div class="form-group">
                        <label>Nom *</label>
                        <input type="text" name="nom"
                               class="form-control"
                               value="<?php echo htmlspecialchars($f['nom']); ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Postnom</label>
                        <input type="text" name="postnom"
                               class="form-control"
                               value="<?php echo htmlspecialchars($f['postnom']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Prénom</label>
                        <input type="text" name="pren"
                               class="form-control"
                               value="<?php echo htmlspecialchars($f['pren']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Dénomination Sociale</label>
                        <input type="text" name="denomSoc"
                               class="form-control"
                               value="<?php echo htmlspecialchars($f['denomSoc']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Téléphone *</label>
                        <input type="text" name="tel"
                               class="form-control"
                               value="<?php echo htmlspecialchars($f['tel']); ?>"
                               required>
                    </div>

                </div>

                <div class="modal-footer border-0">

                    <button type="submit" class="btn btn-success">
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
        <td colspan="5" class="text-center text-muted">
            Aucun fournisseur enregistré.
        </td>
    </tr>

<?php endif; ?>

</tbody>
                </table>

                <div class="d-flex justify-content-between align-items-center mt-3">

    <!-- Texte gauche -->
   <div class="text-muted" style="font-size: 0.95rem;">
        Affichage de <?php echo $showingFrom; ?> 
        à <?php echo $showingTo; ?> 
        sur <?php echo $totalRecords; ?> fournisseur(s)
    </div>
    <!-- Pagination droite -->
    <nav>
        <ul class="pagination mb-0">

            <!-- Previous -->
            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link"
                   href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">
                    Previous
                </a>
            </li>

            <!-- Numéros -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link"
                       href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>

            <!-- Next -->
            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                <a class="page-link"
                   href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">
                    Next
                </a>
            </li>

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



<?php if (isset($_GET['update'])): ?>
    <div class="alert alert-success">
        Fournisseur modifié avec succès.
    </div>
<?php endif; ?>


  <!-- Modal Ajout Fournisseur -->
<div class="modal fade" id="fournisseurModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg rounded">

            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-industry text-primary"></i>
                    Nouveau Fournisseur
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form method="POST" action="create.php"> 

                <div class="modal-body">

                    <div class="form-group">
                        <label>Nom *</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Postnom</label>
                        <input type="text" name="postnom" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Prénom</label>
                        <input type="text" name="pren" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Dénomination Sociale</label>
                        <input type="text" name="denomSoc" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="text" name="tel" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer border-0">

                    <button type="submit" name="btnFournisseur" class="btn btn-success">
                        <i class="fas fa-save"></i> Ajouter
                    </button>

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
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