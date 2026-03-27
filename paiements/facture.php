<?php

session_start();
require_once '../bd/database.php';

// Charger toutes les commandes (même si ici tu ne l'utilises pas)
$sql = "
        SELECT 
            c.idCom,
            c.datCom,

            cl.nom,
            cl.postnom,
            cl.prenom,
            cl.raisSoc,
            cl.tel,

            s.nomSuc,
            s.Comm,

            p.designP,

            d.Qte,
            d.unitMes

        FROM commande c

        INNER JOIN client cl 
            ON c.idClt = cl.idclt

        INNER JOIN succursale s 
            ON c.idSuc = s.idsuc

        INNER JOIN detailscommande d 
            ON c.idCom = d.idcom

        INNER JOIN produit p 
            ON d.idprod = p.idprod

        ORDER BY c.idCom DESC
";

$stmt = $pdo->query($sql);


/* ============================================================
   ✅  REQUÊTE DES LIGNES DE LA FACTURE
   ============================================================ */
$sqlfact = "
SELECT commande.idCom, datCom, nom, postnom, prenom, raisSoc, tel, nomSuc, comm,
designP, caractProduit, detailscommande.Qte, detailscommande.unitMes,
fixationprix.pu, fixationprix.unitMon,
fixationprix.pu * detailscommande.Qte AS PT
FROM Commande
INNER JOIN client ON commande.idClt = client.idclt
INNER JOIN succursale ON commande.idSuc = succursale.idsuc
INNER JOIN detailscommande ON commande.idCom = detailscommande.idcom
INNER JOIN produit ON detailscommande.idprod = produit.idprod
INNER JOIN approvisionnement ON detailscommande.idApprov = approvisionnement.idAprov
INNER JOIN fixationprix ON approvisionnement.idAprov = fixationprix.IdApprov
WHERE commande.idCom = :idco
";


/* ============================================================
   ✅  REQUÊTE POUR LES TOTALS
   ============================================================ */
$sqlfactEnt = "
SELECT *, SUM(PT) AS SousTot FROM (
    SELECT commande.idCom, datCom, nom, postnom, prenom, raisSoc, tel, nomSuc, comm,
    designP, caractProduit, detailscommande.Qte, detailscommande.unitMes,
    fixationprix.pu, fixationprix.unitMon,
    fixationprix.pu * detailscommande.Qte AS PT,
    CONCAT('FAC', ' ', YEAR(datCom), ' ', commande.idCom) AS NumFact
    FROM Commande
    INNER JOIN client ON commande.idClt = client.idclt
    INNER JOIN succursale ON commande.idSuc = succursale.idsuc
    INNER JOIN detailscommande ON commande.idCom = detailscommande.idcom
    INNER JOIN produit ON detailscommande.idprod = produit.idprod
    INNER JOIN approvisionnement ON detailscommande.idApprov = approvisionnement.idAprov
    INNER JOIN fixationprix ON approvisionnement.idAprov = fixationprix.IdApprov
    WHERE commande.idCom = :idco
) rqt
";


/* ============================================================
   ✅  EXÉCUTION DES REQUÊTES
   ============================================================ */
$res = $pdo->prepare($sqlfact);
$resEnt = $pdo->prepare($sqlfactEnt);

$res->execute([
    ':idco' => $_GET['idCom']
]);

$resEnt->execute([
    ':idco' => $_GET['idCom']
]);

$com = $res->fetchAll();      // toutes les lignes de la facture
$comEnt = $resEnt->fetchAll(); // total + infos facture

// ✅ prendre la première ligne pour les infos client/date
$co = count($com) > 0 ? $com[0] : null;

$compteur = 0;

$no   = isset($co['nom']) ? $co['nom'] : '';
$idco = isset($co['idCom']) ? $co['idCom'] : '';



/* ============================================================
   ✅  VÉRIFICATION DU PAIEMENT (ruban rouge / vert)
   ============================================================ */
$sqlPay = "SELECT COUNT(*) AS total FROM paiement WHERE idCom = :idCom";
$resPay = $pdo->prepare($sqlPay);
$resPay->execute([":idCom" => $_GET['idCom']]);
$dataPay = $resPay->fetch();

// ✅ Si au moins un paiement existe → payé
$estPaye = ($dataPay["total"] > 0);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>BISIKOMASH - Facturation</title>

     <link rel="shortcut icon" href="/gestion_quincaillerie/img/icone.ico" type="image/x-icon">
    <link rel="icon" href="/gestion_quincaillerie/img/icone.ico" type="image/x-icon">

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
        <link rel="stylesheet" href="../assets/css/style.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
    rel="stylesheet"
>

    <!-- Custom styles for this template-->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        
        .table-commandes td,
.table-commandes th{
    font-size:13px;
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



<script>

$(document).ready(function(){

        $('#tableCommandes').DataTable({

                "pageLength": 10,
                "ordering": true,
                "searching": true

        });

});

</script>
                   

                </div>
                <!-- /.container-fluid -->

            </div>


<style>

.invoice-container{
    width:100%;
    margin:auto;
    background:#ffffff;
    padding:30px;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:linear-gradient(90deg,#0a87b8,#2bb3e3);
    color:#ffffff;
    padding:20px;
}

.logo img{
    width:190px;
}

.title{
    
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    font-size: 40px;
    font-weight: bold;
    color: #ffffff;
    letter-spacing: 2px;
    z-index: 1;

}

.info-section{
    display:flex;
    justify-content:space-between;
    margin-top:30px;
}

.items-table{
    width:100%;
    border-collapse:collapse;
    margin-top:30px;
}

.items-table th{
    background:#1fa2cf;
    color:#ffffff;
    padding:12px;
    text-align:left;
}

.items-table td{
    padding:12px;
    border-bottom:1px solid #dddddd;
}

.items-table tbody tr:nth-child(even){
    background:#f1f1f1;
}

.totals{
    display:flex;
    justify-content:space-between;
    margin-top:40px;
}

.total-box{
    text-align:right;
}

.grand-total{
    background:#1fa2cf;
    color:#ffffff;
    padding:15px;
    font-size:20px;
    font-weight:bold;
    margin-top:10px;
}

.footer{
    margin-top:40px;
    border-top:1px solid #dddddd;
    padding-top:20px;
}

.facture-actions{
    display:flex;
    justify-content:flex-end;
    margin-bottom:15px;
}

/* Impression */

@media print {

    /* conserver les couleurs lors de l'impression */

    *{
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* cacher tout le contenu de la page */

    body *{
        visibility: hidden;
    }

    /* afficher uniquement la facture */

    #zoneFacture,
    #zoneFacture *{
        visibility: visible;
    }

    /* positionner la facture pour l'impression */

    #zoneFacture{
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    /* masquer les boutons et actions */

    .facture-actions{
        display: none;
    }

   

}

.header {
    position: relative;
    overflow: hidden; /* IMPORTANT pour couper proprement le ruban */

}

.ribbon {
    position: absolute;
    top: 18px;
    right: -42px;
    width: 160px;
    text-align: center;

    color: #fff;
    font-weight: bold;
    font-size: 14px;
    letter-spacing: 1px;

    padding: 8px 0;
    transform: rotate(45deg);

    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
}

/* Ruban vert */
.ribbon-paid {
    background-color: #158a16;
}

/* Ruban rouge */
.ribbon-unpaid {
    background-color: #c0392b;
}

</style>

<!-- zone imprimable -->

<div class="facture-actions d-flex gap-2">
        <a href="index.php" 
                class="btn btn-primary ml-10"
                >

            <i class="fa-solid fa-arrow-left"></i>
            Retour

        </a>   

         <a href="../paiements/facturePayee.php?clt=<?php echo $_GET['clt']; ?>
    &PrixT=<?php echo $_GET['PrixT']; ?>&idCom=<?php echo $_GET['idCom']; ?>&unitMon=<?php echo $_GET['unitMon']; ?>"
                class="btn btn-primary ml-10"                
                
                >
            <i class="fa-solid fa-dollar-sign"></i>
            Payer
        </a>                       

        <button 
                class="btn btn-success"
                onclick="window.print()">

            <i class="fas fa-print"></i>
            Imprimer

        </button>

    </div>
    

<div id="zoneFacture">
     

    


    <div class="invoice-container">

        <div class="header">

            <div class="logo">
                <img src="../img/bisikomashLogo1.png" alt="Logo entreprise">
            </div>

            <div class="title">
                FACTURE
            </div>

            <?php if ($estPaye): ?>
                <div class="ribbon ribbon-paid">PAYÉ</div>
            <?php else: ?>
                <div class="ribbon ribbon-unpaid">NON PAYÉ</div>
            <?php endif; ?>


        </div>

       <div class="info-section">
            <?php if (count($comEnt) > 0) :  ?>
            <div class="invoice-to">
                

                <h4>FACTURÉ À :</h4>
                
                <p>Client : <?php echo $_GET['clt'] ?></p>
                <p>Avenue Industrielle, Quartier Dilala</p>
                <p>Commune de Dilala, Kolwezi</p>
                <?php foreach ($comEnt as $cmde) : ?>
                <p>Tél : <?php echo $cmde['tel'] ?></p>


            </div>


            <div class="invoice-details">

                <p><strong>N° Facture : </strong><?php echo $cmde['NumFact']?></p>
                <p><strong>Date :</strong> <?php echo $cmde['datCom']?></p>
                <p><strong>Lieu :</strong> Kolwezi, Lualaba</p>
            <?php endforeach; ?>

            </div>
            <?php endif; ?>
        </div>

        <?php if (count($com) > 0) :  ?>
        <table class="items-table">

            <thead>

                <tr>
                    <th>N°</th>
                    <th>DÉSIGNATION DU PRODUIT</th>
                    <th>PRIX UNITAIRE (CDF)</th>
                    <th>QUANTITÉ</th>
                    <th>MONTANT TOTAL</th>
                </tr>

            </thead>


            <tbody>
                <?php foreach ($com as $cmd) : $compteur++ ?>
                <tr>
                    <td><?php echo $compteur ?></td>
                    <td><?php echo $cmd['designP'].' '.$cmd['caractProduit'] ?></td>
                    <td><?php echo $cmd['pu'].' '.$cmd['unitMon'] ?></td>
                    <td><?php echo $cmd['Qte'].' '.$cmd['unitMes'] ?></td>
                    <td><?php echo ($cmd['pu']*$cmd['Qte']).' '.$cmd['unitMon'] ?></td>
                </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
        <?php endif; ?>


        <div class="totals">

            <div class="payment">

                <h4>Informations de paiement :</h4>

                <p>Entreprise : BISIKOMASH QUINCAILLERIE</p>
                <p>Compte bancaire : 0123456789</p>
                <p>Banque : RAWBANK Kolwezi</p>
                <p>Téléphone : +243 850 754 604</p>

            </div>

            <?php if (count($comEnt) > 0) :  ?>
            <div class="total-box">
                 <?php foreach ($comEnt as $cmde) : ?>
                <p>SOUS TOTAL : <?php echo $cmde['SousTot'].' '.$cmde['unitMon']  ?></p>
                <p>TVA : 0 %</p>

                <div class="grand-total">
                    TOTAL À PAYER : <?php echo $cmde['SousTot'].' '.$cmde['unitMon']  ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </div>

        <div class="footer">

            <h3>Merci pour votre confiance</h3>

            <p>
                BISIKOMASH QUINCAILLERIE  
                Vente de matériaux de construction et fournitures industrielles  
                Kolwezi – Province du Lualaba – République Démocratique du Congo
            </p>

        </div>

    </div>

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

<div class="modal fade" id="factureModal" tabindex="-1">

    <div class="modal-dialog modal-xl">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Facture
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">



<!-- Modal effectuer le paiement -->
<div class="modal fade" id="paiementModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg rounded">

            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-industry text-primary"></i>
                    Nouveau paiement
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

<style>

.invoice-container{
    width:100%;
    margin:auto;
    background:#ffffff;
    padding:30px;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:linear-gradient(90deg,#0a87b8,#2bb3e3);
    color:#ffffff;
    padding:20px;
}

.logo img{
    width:190px;
}

.title{
    font-size:40px;
    font-weight:bold;
}

.info-section{
    display:flex;
    justify-content:space-between;
    margin-top:30px;
}

.items-table{
    width:100%;
    border-collapse:collapse;
    margin-top:30px;
}

.items-table th{
    background:#1fa2cf;
    color:#ffffff;
    padding:12px;
    text-align:left;
}

.items-table td{
    padding:12px;
    border-bottom:1px solid #dddddd;
}

.items-table tbody tr:nth-child(even){
    background:#f1f1f1;
}

.totals{
    display:flex;
    justify-content:space-between;
    margin-top:40px;
}

.total-box{
    text-align:right;
}

.grand-total{
    background:#1fa2cf;
    color:#ffffff;
    padding:15px;
    font-size:20px;
    font-weight:bold;
    margin-top:10px;
}

.footer{
    margin-top:40px;
    border-top:1px solid #dddddd;
    padding-top:20px;
}

.facture-actions{
    display:flex;
    justify-content:flex-end;
    margin-bottom:15px;
}

/* Impression */

@media print {

    /* conserver les couleurs lors de l'impression */

    *{
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* cacher tout le contenu de la page */

    body *{
        visibility: hidden;
    }

    /* afficher uniquement la facture */

    #zoneFacture,
    #zoneFacture *{
        visibility: visible;
    }

    /* positionner la facture pour l'impression */

    #zoneFacture{
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    /* masquer les boutons et actions */

    .facture-actions{
        display: none;
    }

   

}

</style>

<!-- zone imprimable -->


            </div>

        </div>

    </div>

</div>

<script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
></script>
    <script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>

</body>

</html>