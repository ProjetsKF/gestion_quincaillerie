<?php
//session_start();
require_once '../bd/database.php';

/* Sécurité : recruteur uniquement 
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header('Location: ../login.php');
    exit;
}
*/

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $designP  = trim($_POST['designP']);
    $caractProduit = trim($_POST['caractProduit']);

    if ($designP && $caractProduit) {

        $sql = "INSERT INTO produit
                (designP, caractProduit)
                VALUES
                (:designP, :caractProduit)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':designP'       => $designP,
            ':caractProduit' => $caractProduit
        ]);

        $message = "Produit enregistré avec succès.";
        $message_type = 'success';

    } else {
        $message = "Tous les champs sont obligatoires.";
        $message_type = 'error';
    }
}


/* ===============================
   AFFICHAGE DES PRODUITS
================================= */

$sql = "SELECT * FROM produit LIMIT 5";

$res = $pdo->prepare($sql);
$res->execute();

$prod = $res->fetchAll();

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>BISIKOMASH - Produits</title>

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
                Gestion des Produits
            </h6>

            <button class="btn btn-primary btn-sm"
                    data-toggle="modal"
                    data-target="#produitModal">
                <i class="fas fa-plus"></i> Nouveau Produit
            </button>
        </div>

        <!-- Body -->
        <div class="card-body">
            <form method="post" action="recherche.php">
                <div class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="txtRech" 
                               placeholder="Rechercher un produit...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary" name="btnRecherche">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
            </form>

            <!-- Barre de recherche -->
            

            <!-- Tableau -->
            <div class="table-responsive">
                <?php if (count($prod) > 0) : ?>
                <table class="table table-hover align-items-center">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Désignation</th>
                            <th>Caractéristiques</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($prod as $pr) : ?>
                        <tr>
                            <td><?= htmlspecialchars($pr['idprod']) ?></td>
                            <td><?= htmlspecialchars($pr['designP']) ?></td>
                            <td><?= htmlspecialchars($pr['caractProduit']) ?></td>
                            <td class="text-center">
                                <a href="#" class="text-primary mr-3">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" class="text-danger">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
                <?php endif; ?>
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


  <!-- Modal Ajout Produit -->
<div class="modal fade" id="produitModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg rounded">

            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-box text-primary"></i>
                    Nouveau Produit
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <?php if (!empty($message)) : ?>
                    <div class="card-panel
                        <?= $message_type === 'error' ? 'red lighten-4' : 'green lighten-4' ?>">
                        <span class="
                            <?= $message_type === 'error'
                                ? 'red-text text-darken-4'
                                : 'green-text text-darken-4' ?>">
                            <i class="material-icons left">
                                <?= $message_type === 'error' ? 'error' : 'check_circle' ?>
                            </i>
                            <?= htmlspecialchars($message) ?>
                        </span>
                    </div>
                <?php endif; ?>
                


                <form method="post" action="create.php">

                <form method="post">

                    <div class="form-group">
                        <label>Désignation *</label>
                        <input type="text" class="form-control" name="designP" 
                               placeholder="Ex: Clou 3 pouces">
                    </div>

                    <div class="form-group">
                        <label>Caractéristiques</label>
                        <textarea class="form-control"
                                  rows="3"
                                  placeholder="Ex: Acier galvanisé, 3 pouces" name="caractProduit"></textarea>
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

            </div>

            

        </div>
    </div>
</div>
   <script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>

</body>

</html>