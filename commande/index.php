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

    $nom  = trim($_POST['nom']);
    $postnom = trim($_POST['postnom']);
    $prenom = trim($_POST['prenom']);
    $raisSoc = trim($_POST['raisSoc']);
    $tel = trim($_POST['tel']);
    

    if ($nom && $postnom && $prenom && $raisSoc && $tel   ) {

        $sql = "INSERT INTO Client
                (nom, postnom, prenom, raisSoc, tel)
                VALUES
                (:nom, :postnom, :prenom, :raisSoc, :tel)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom'       => $nom,
            ':postnom'   => $postnom,
            ':prenom'    => $prenom,
            ':raisSoc'   => $raisSoc,
            ':tel'       => $tel           
        ]);

        $message = "Produit enregistré avec succès.";
        $message_type = 'success';

    } else {
        $message = "Tous les champs sont obligatoires.";
        $message_type = 'Erreur';
    }
}


$sql = "SELECT * FROM Client LIMIT 5";

$res= $pdo->prepare($sql);
$res->execute([
]);

$clt = $res->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>BISIKOMASH - Clients</title>

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
        <a href="/gestion_quincaillerie/index.php" class="text-secondary">
            <i class="fas fa-arrow-left"></i> Retour au tableau de bord
        </a>
    </div>

    <!-- Card principale -->
    <div class="card shadow mb-4">

        <!-- Header -->
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Gestion des Clients
            </h6>

            <button class="btn btn-primary btn-sm"
                    data-toggle="modal"
                    data-target="#clientModal">
                <i class="fas fa-plus"></i> Nouveau Client
            </button>
        </div>

        <!-- Body -->
        <div class="card-body">

            <!-- Recherche -->
            <div class="mb-4">
                <div class="input-group">
                    <input type="text" class="form-control"
                           placeholder="Rechercher par nom, société ou téléphone...">
                    <div class="input-group-append">
                        <button class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tableau -->
            <?php if (count($clt) > 0) : ?>
            <div class="table-responsive">
                <table class="table table-hover align-items-center">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Nom Complet</th>
                            <th>Raison Sociale</th>
                            <th>Téléphone</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($clt as $cl) : ?>
                        <tr>
                            <td><?= htmlspecialchars($cl['idclt']) ?></td>
                            <td><?= htmlspecialchars($cl['nom'].' '.$cl['postnom'].' '.$cl['prenom']) ?></td>
                            <td><?= htmlspecialchars($cl['raisSoc']) ?></td>
                            <td><i class="fas fa-phone mr-1 text-muted"></i> <?= htmlspecialchars($cl['tel']) ?></td>
                            <td class="text-center">
                                 <a href="#" class="text-primary mr-3">
                                    <i class="fas fa-shopping-cart mr-2"></i>
                                </a>
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


 <!-- Modal Ajout Client -->
<div class="modal fade" id="clientModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg rounded">

            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-user text-primary"></i>
                    Nouveau Client
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <form method="post">

                    <div class="form-group">
                        <label>Nom *</label>
                        <input type="text" class="form-control" name="nom" 
                               placeholder="Ex: KASONGO">
                    </div>

                    <div class="form-group">
                        <label>Postnom</label>
                        <input type="text" class="form-control" name="postnom" 
                               placeholder="Ex: BANZA">
                    </div>

                    <div class="form-group">
                        <label>Prénom</label>
                        <input type="text" class="form-control" name="prenom" 
                               placeholder="Ex: Patrick">
                    </div>

                    <div class="form-group">
                        <label>Raison Sociale</label>
                        <input type="text" class="form-control" name="raisSoc" 
                               placeholder="Ex: BISIKOMASH SARL">
                    </div>

                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="text" class="form-control" name="tel" 
                               placeholder="Ex: 0994123456">
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