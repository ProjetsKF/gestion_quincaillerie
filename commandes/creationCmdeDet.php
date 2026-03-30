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
$msg = '';
$message = '';
$message_type = '';

$sql = "SELECT DISTINCT *,SUM(PT)as PrixT from (SELECT c.idCom,c.datCom,cl.nom,cl.postnom,cl.prenom,cl.raisSoc,cl.tel,s.nomSuc,s.comm,p.designP,p.caractProduit,d.Qte,d.unitMes,f.pu,f.unitMon, f.pu*d.Qte as PT FROM Commande c INNER JOIN client cl on c.idClt=cl.idclt INNER JOIN succursale s ON c.idSuc=s.idsuc INNER JOIN detailscommande d ON c.idCom=d.idcom INNER JOIN produit p on d.idprod=p.idprod INNER JOIN approvisionnement a ON d.idApprov=a.idAprov INNER JOIN fixationprix f on a.idAprov=f.IdApprov)rqt WHERE idcom=:idcom GROUP BY idcom ";

$somFact= $pdo->prepare($sql);
$somFact->execute([
    ':idcom'       =>$_GET['idcmd']
]);

$som = $somFact->fetchAll(PDO::FETCH_ASSOC);

$sqlCom = "SELECT designP,caractProduit,seuil_min,COALESCE(a.totEntree,0)-COALESCE(c.totSortie,0) as Stock FROM produit p LEFT JOIN (SELECT idprod,idAprov,SUM(approvisionnement.Qte) as totEntree FROM approvisionnement GROUP BY idprod,idAprov)a On p.idprod=a.idProd LEFT join (SELECT idprod,idApprov,SUM(detailscommande.Qte) as totSortie from detailscommande GROUP BY idprod,idApprov)c ON a.idAprov= c.idApprov where CONCAT(designP,' ',caractProduit)=:designP AND a.idAprov=:idAprov";

$verQte= $pdo->prepare($sqlCom);
$verQte->execute([
    ':designP'       =>$_GET['prod'],
    ':idAprov'       => $_GET['idApp']
]);

$ver = $verQte->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $Qte = trim($_POST['Qte']);
    $unitMes = trim($_POST['unitMes']);
    $idprod = $_GET['prod'];
    $idApprov = $_GET['idApp'];


    if ($Qte && $unitMes) {
        $checkStock = "SELECT designP,caractProduit,seuil_min,COALESCE(a.totEntree,0)-COALESCE(c.totSortie,0) as Stock FROM produit p LEFT JOIN (SELECT idprod,idAprov,SUM(approvisionnement.Qte) as totEntree FROM approvisionnement GROUP BY idprod,idAprov)a On p.idprod=a.idProd LEFT join (SELECT idprod,idApprov,SUM(detailscommande.Qte) as totSortie from detailscommande GROUP BY idprod,idApprov)c ON a.idAprov= c.idApprov where CONCAT(designP,' ',caractProduit)=:designP AND a.idAprov=:idAprov  AND COALESCE(a.totEntree,0)-COALESCE(c.totSortie,0)<$Qte";
        $checkStmt = $pdo->prepare($checkStock);
        $checkStmt->execute([
            ':designP' => $_GET['prod'],
            ':idAprov' => $_GET['idApp']
        ]);

        if ($checkStmt->fetch()) {

            $msg = "La quantité commandée est supérieure au stock de ce produit.";
            $message_type = 'Erreur';

        } 
        else{
            $sql = "INSERT INTO detailscommande
                (idcom, idprod,Qte,unitMes,idApprov)
                VALUES
                (:idCom,(SELECT idprod from Produit where CONCAT(designP,' ',caractProduit)=:idprod ),:Qte,:unitMes,:idApprov)";
                $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':idCom'        =>$_GET['idcmd'],
            ':idprod'       => $_GET['prod'],
            ':Qte'          => $Qte,
            ':unitMes'      => $unitMes,
            ':idApprov'      => $idApprov
        ]);

        $message = "Produit enregistré avec succès.";
        $message_type = 'success';
        header('Location:../commandes/produits.php?idclt='.$_GET['idclt'].'&idcmd='.$_GET['idcmd'].'&PrixT='.$som['PrixT']);
        }

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

    <link rel="shortcut icon" href="/gestion_quincaillerie/img/icone.ico" type="image/x-icon">
    <link rel="icon" href="/gestion_quincaillerie/img/icone.ico" type="image/x-icon">

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
               Détails commande
            </h6>

<!-- Bouton Actualiser -->
       
        </div>

        <!-- Body -->
        <div class="card-body">
            

            <!-- Barre de recherche -->
            

            <!-- Tableau -->
            <div class="table-responsive">
                <form method="post">
                    <?php if (!empty($msg)) : ?>
                                    <div class="card-panel 
                                        <?= $message_type === 'error' ? 'red lighten-4' : 'green lighten-4' ?>">
                                        
                                        <span class="
                                            <?= $message_type === 'error' 
                                                ? 'red-text text-darken-4' 
                                                : 'green-text text-darken-4' ?>">
                                            
                                            <i class="material-icons left">
                                                <?= $message_type === 'error' ? 'error' : 'check_circle' ?>
                                            </i>
                                            <?= htmlspecialchars($msg) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                    <div class="form-group">
                        <?php foreach ($ver as $sto) : ?>
                        <label>Stock disponible *</label>
                        <input type="text"  class="form-control" name="Qte" 
                               value="<?php echo $sto['Stock']; ?>" readonly="true" bg-success text-red>
                               <?php endforeach; ?>
                    </div>

                    <div class="form-group">
                        <label>Quantité *</label>
                        <input type="text" class="form-control" name="Qte" 
                               placeholder="Ex: 10 kg, 10 Bidons, 10 pièces, 10 cartons, etc">
                    </div>

                     <div class="form-group">

                    <label>Unité de mesure *</label>

                    <input type="text"
                           class="form-control"
                           name="unitMes"
                           value="<?= isset($_GET['unitMes']) ? htmlspecialchars($_GET['unitMes']) : '' ?>"
                           placeholder="Ex: Kg, pièces, carton, bidons, etc."
                           readonly>
                </div>

                     <div class="form-group">
                        <label>Produit *</label>
                        <input type="text" name="idpro"  class="form-control" id="targetInput" value="<?= $_GET['prod'] ?>" 
                               placeholder="Ex: Clou 3 pouces" readonly="true" >
                               <script>
                                    $(document).ready(function(){
                                        $('#produitModal').on('show.bs.modal', function(event){
                                            var button=$(event.relatedTarget);
                                            var recId=button.data('id');
                                            var modal=$(this).prop('id');
                                            modal.find('#targetInput').val(recId);  
                                    });
                                 });
                                   
                               </script>
                    </div>

                     <div class="form-group">
                        <label>Client *</label>
                        <input type="text" name="idclt" value="<?= $_GET['idclt'] ?>" class="form-control" readonly="true"
                               placeholder="Ex: BISIKOMASH SARL">
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