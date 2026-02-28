<?php
require_once '../bd/database.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {

    $sql = "SELECT * FROM succursale
            WHERE nomSuc LIKE :search
            OR Quart LIKE :search
            OR Comm LIKE :search
            OR Aven LIKE :search
            ORDER BY idsuc DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':search' => "%$search%"]);

} else {

    $sql = "SELECT * FROM succursale
            ORDER BY idsuc DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}

$succursales = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>BISIKOMASH - Succursales</title>

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

<!-- LES MESSAGES -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 'updated'): ?>
    <div class="alert alert-success">
        Succursale mise à jour avec succès.
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
    <div class="alert alert-success">
        Succursale supprimée avec succès.
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] == 'used'): ?>
        <div class="alert alert-danger">
            Impossible de supprimer : cette succursale est utilisée dans un approvisionnement.
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
            Gestion des Succursales
        </h6>

        <div class="btn-group">

            <!-- Bouton Actualiser -->
            <a href="index.php" class="btn btn-outline-secondary btn-sm mr-2">
                <i class="fas fa-sync-alt"></i> Actualiser
            </a>

            <!-- Bouton Nouvelle Succursale -->
            <button class="btn btn-primary btn-sm"
                    data-toggle="modal"
                    data-target="#succursaleModal">
                <i class="fas fa-plus"></i> Nouvelle Succursale
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
                   placeholder="Rechercher une succursale..."
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
                            <th>Nom</th>
                            <th>Quartier</th>
                            <th>Commune</th>
                            <th>Avenue</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

<?php if (count($succursales) > 0): ?>

<?php foreach ($succursales as $s): ?>
<tr>
    <td><?php echo $s['idsuc']; ?></td>

    <td>
        <i class="fas fa-store mr-1 text-muted"></i>
        <?php echo htmlspecialchars($s['nomSuc']); ?>
    </td>

    <td><?php echo htmlspecialchars($s['Quart']); ?></td>
    <td><?php echo htmlspecialchars($s['Comm']); ?></td>
    <td><?php echo htmlspecialchars($s['Aven']); ?></td>

    <td class="text-center">

        <a href="#"
           data-toggle="modal"
           data-target="#editModal<?php echo $s['idsuc']; ?>"
           class="text-primary mr-3">
            <i class="fas fa-edit"></i>
        </a>

        <a href="delete.php?id=<?php echo $s['idsuc']; ?>"
           class="text-danger"
           onclick="return confirm('Supprimer cette succursale ?');">
            <i class="fas fa-trash"></i>
        </a>

    </td>
</tr>

<!-- Modal Modifier Succursale -->
<div class="modal fade"
     id="editModal<?php echo $s['idsuc']; ?>"
     tabindex="-1"
     role="dialog">

    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg rounded">

            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-edit text-primary"></i>
                    Modifier Succursale
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form method="POST" action="update.php">

                <!-- ID caché -->
                <input type="hidden"
                       name="idsuc"
                       value="<?php echo $s['idsuc']; ?>">

                <div class="modal-body">

                    <div class="form-group">
                        <label>Nom de la succursale *</label>
                        <input type="text"
                               name="nomSuc"
                               class="form-control"
                               value="<?php echo htmlspecialchars($s['nomSuc']); ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Quartier</label>
                        <input type="text"
                               name="Quart"
                               class="form-control"
                               value="<?php echo htmlspecialchars($s['Quart']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Commune</label>
                        <input type="text"
                               name="Comm"
                               class="form-control"
                               value="<?php echo htmlspecialchars($s['Comm']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Avenue</label>
                        <input type="text"
                               name="Aven"
                               class="form-control"
                               value="<?php echo htmlspecialchars($s['Aven']); ?>">
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
    <td colspan="6" class="text-center text-muted">
        Aucune succursale enregistrée.
    </td>
</tr>
<?php endif; ?>

</tbody>

                </table>
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



<!-- Modal Ajout Succursale -->
<div class="modal fade" id="succursaleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg rounded">

            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-building text-primary"></i>
                    Nouvelle Succursale
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- FORM COMMENCE ICI -->
            <form method="POST" action="create.php">

                <div class="modal-body">

                    <div class="form-group">
                        <label>Nom de la succursale *</label>
                        <input type="text"
                               name="nomSuc"
                               class="form-control"
                               placeholder="Ex: Succursale Centre"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Quartier</label>
                        <input type="text"
                               name="Quart"
                               class="form-control"
                               placeholder="Ex: Kenya">
                    </div>

                    <div class="form-group">
                        <label>Commune</label>
                        <input type="text"
                               name="Comm"
                               class="form-control"
                               placeholder="Ex: Lubumbashi">
                    </div>

                    <div class="form-group">
                        <label>Avenue</label>
                        <input type="text"
                               name="Aven"
                               class="form-control"
                               placeholder="Ex: Av. Lumumba">
                    </div>

                </div>

                <div class="modal-footer border-0">

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Ajouter
                    </button>

                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        Annuler
                    </button>

                </div>

            </form>
            <!-- FORM FINI ICI -->

        </div>
    </div>
</div>
   <script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>

</body>

</html>