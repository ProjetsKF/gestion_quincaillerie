<?php
session_start();
require_once '../bd/database.php';

if (!isset($_SESSION['idsuc'])) {
    header("Location: ../index.php");
    exit;
}

/* Sécurité : recruteur uniquement 
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header('Location: ../login.php');
    exit;
}
*/

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idCom = trim($_POST['idCom']);

   
    if ($idCom) {

        $sqlComde = "INSERT INTO commande
                (idCom,idSuc,idClt)
                VALUES
                (:idCom,:idSuc,(SELECT idclt from Client where CONCAT( nom,' ',postnom,' ',prenom)=:idClt))";

        $stmt1 = $pdo->prepare($sqlComde);
        $stmt1->execute([
            ':idCom'       => $idCom,
            ':idSuc'       => $_SESSION['idsuc'],
            ':idClt'       => $_GET['idclt']
        ]);


        $message = "Produit enregistré avec succès.";
        $message_type = 'success';
        header('Location:../commandes/produits.php?idcmd='.$idCom.'&idclt='.$_GET['idclt']);

    } else {
        $message = "Tous les champs sont obligatoires.";
        $message_type = 'error';
    }
}


/* ===============================
   N° AUTO COMMANDES
================================= */

 $sqlRqt = "SELECT CASE WHEN (SELECT max(idCom)+1  FROM commande) IS NULL then 1 else (SELECT max(idCom)+1  FROM commande) END as idCom";
        $stmt = $pdo->prepare($sqlRqt);
        $stmt->execute();

        $cmd = $stmt->fetchAll();

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>BISIKOMASH - Commande</title>

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
               Commande
            </h6>

<!-- Bouton Actualiser -->
       
            <button class="btn btn-primary btn-sm"
                    data-toggle="modal"
                    data-target="#produitModal">
                <i class="fas fa-plus"></i> Nouvelle commande
            </button>
        </div>

        <!-- Body -->
        <div class="card-body">
            

            <!-- Barre de recherche -->
            

            <!-- Tableau -->
            <div class="table-responsive">
                <form method="post">

                    <div class="form-group">
                        <label>N° Commande *</label>
                        <?php foreach ($cmd as $cm) : ?>
                        <input type="text" class="form-control" name="idCom" value="<?= htmlspecialchars($cm['idCom']) ?>" 
                               placeholder="Ex: 10 kg, 10 Bidons, 10 pièces, 10 cartons, etc" readonly="true">
                        <?php endforeach; ?>
                    </div>

                    <div class="form-group">
                        <label>Client *</label>
                        <input type="text" name="idclt" value="<?= $_GET['idclt'] ?>" class="form-control" 
                               placeholder="Ex: BISIKOMASH SARL" readonly="true">
                    </div>
                     

                    <div class="modal-footer border-0">

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Créer Commande
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


<div class="modal fade" id="produitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg rounded">

            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Détails commande
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

                

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