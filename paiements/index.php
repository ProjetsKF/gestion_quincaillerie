<?php

session_start();
require_once '../bd/database.php';

/* ===============================
   RECHERCHE
================================= */

$search = isset($_GET['txtRech']) ? trim($_GET['txtRech']) : '';

/* ===============================
   PAGINATION
================================= */

$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$start = ($page - 1) * $limit;


/* ===============================
   TOTAL COMMANDES
================================= */

$countSql = "
    SELECT COUNT(*)
    FROM commande c
    LEFT JOIN client cl ON c.idClt = cl.idclt
";

if (!empty($search)) {

    $countSql .= "
        WHERE cl.nom LIKE :search
        OR cl.postnom LIKE :search
        OR cl.prenom LIKE :search
        OR cl.raisSoc LIKE :search
        OR cl.tel LIKE :search
        OR c.datCom LIKE :search
    ";
}

$stmtCount = $pdo->prepare($countSql);

if (!empty($search)) {
    $stmtCount->bindValue(':search', "%$search%", PDO::PARAM_STR);
}

$stmtCount->execute();

$totalRecords = $stmtCount->fetchColumn();

$totalPages = ceil($totalRecords / $limit);


/* ===============================
   RECUPERATION DES COMMANDES
================================= */

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
        s.Comm

    FROM commande c

    LEFT JOIN client cl
        ON c.idClt = cl.idclt

    LEFT JOIN succursale s
        ON c.idSuc = s.idsuc
";

if (!empty($search)) {

    $sql .= "
        WHERE cl.nom LIKE :search
        OR cl.postnom LIKE :search
        OR cl.prenom LIKE :search
        OR cl.raisSoc LIKE :search
        OR cl.tel LIKE :search
        OR c.datCom LIKE :search
    ";
}

$sql .= "
    ORDER BY c.idCom DESC
    LIMIT :start, :limit
";

$stmt = $pdo->prepare($sql);

if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}

$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

$stmt->execute();

$commandes = $stmt->fetchAll();


/* ===============================
   INFORMATION AFFICHAGE
================================= */

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



<nav>
        <ul class="pagination justify-content-end">

        <?php if ($page > 1): ?>
            <li class="page-item">
            <a class="page-link" href="?page=<?php echo $page-1; ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <li class="page-item">
            <a class="page-link" href="?page=<?php echo $page+1; ?>">Next</a>
            </li>
        <?php endif; ?>

        </ul>
</nav>

<div class="card-header py-3 d-flex justify-content-between align-items-center">

    <h6 class="m-0 font-weight-bold text-primary">
        Facturation
    </h6>

    <div>

        <!-- Bouton Actualiser -->
        <a href="index.php" class="btn btn-secondary btn-sm mr-2">
            <i class="fas fa-sync-alt"></i> Actualiser
        </a>

    </div>

</div>
 <!-- MESSAGES  -->

<?php if (isset($_GET['deleted'])): ?>

<div class="alert alert-success alert-dismissible fade show">

    <i class="fas fa-check-circle"></i>
    Facture supprimée avec succès.

    <button type="button"
            class="close"
            data-dismiss="alert">
        <span>&times;</span>
    </button>

</div>

<script>

if (window.location.search.includes("deleted")) {

    const url = new URL(window.location);

    url.searchParams.delete("deleted");

    window.history.replaceState({}, document.title, url.pathname);

}

</script>




<?php endif; ?>
<table class="table table-bordered table-hover table-sm table-commandes">

 <!-- RECHERCHE FACTURE  -->
        <form method="GET">

                    <div class="mb-4">

                    <div class="input-group">

                    <input type="text"
                           class="form-control"
                           name="txtRech"
                          placeholder="Rechercher par client, société, téléphone, date..."
                           value="<?php echo isset($_GET['txtRech']) ? htmlspecialchars($_GET['txtRech']) : ''; ?>">

                    <div class="input-group-append">

                    <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    </button>

                    </div>

                    </div>

                    </div>

            </form>

        <thead>

            <tr>

                <th>N°</th>
                <th>Date</th>
                <th>Client</th>
                <th>Société</th>
                <th>Téléphone</th>
                <th>Succursale</th>
                <th>Commune</th>
                <th>Action</th>

            </tr>

</thead>
<?php $cpteur = 0; ?>

      <tbody>

<?php foreach ($commandes as $row): $cpteur++; ?>

<tr>

    <td><?php echo $cpteur; ?></td>

    <td><?php echo $row['datCom']; ?></td>

    <td>
        <i class="fas fa-user"></i>
        <?php echo $row['nom']." ".$row['postnom']." ".$row['prenom']; ?>
    </td>

    <td><?php echo $row['raisSoc']; ?></td>

    <td>
        <i class="fas fa-phone"></i>
        <?php echo $row['tel']; ?>
    </td>

    <td>
        <i class="fas fa-building"></i>
        <?php echo $row['nomSuc']; ?>
    </td>

    <td><?php echo $row['Comm']; ?></td>

    <td>

    <!-- Bouton Facture -->

    <a href="facture.php?clt=<?php echo $row['nom'].' '.$row['postnom'].' '.$row['prenom']; ?>
    &dat=<?php echo $row['datCom']; ?>
    &tel=<?php echo $row['tel']; ?>
    &idCom=<?php echo $row['idCom']; ?>"

    class="btn btn-primary btn-sm">

        <i class="fas fa-file-invoice"></i>
        Facture

    </a>


    <!-- Bouton Supprimer -->

    <a href="deleteFacture.php?idCom=<?php echo $row['idCom']; ?>"
       class="btn btn-danger btn-sm"
       onclick="return confirm('Supprimer cette facture ?');">

        <i class="fas fa-trash"></i>

    </a>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<?php if(empty($commandes)): ?>

<tr>
<td colspan="8" class="text-center text-muted">
Aucune facture trouvée
</td>
</tr>

<?php endif; ?>

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

<div id="zoneFacture">

    <div class="facture-actions">

        <button 
                class="btn btn-success"
                onclick="window.print()">

            <i class="fas fa-print"></i>
            Imprimer

        </button>

    </div>


    <div class="invoice-container">

        <div class="header">

            <div class="logo">
                <img src="../img/bisikomashLogo1.png" alt="Logo entreprise">
            </div>

            <div class="title">
                FACTURE
            </div>

        </div>

        <div class="info-section">

            <div class="invoice-to">
                

                <h4>FACTURÉ À :</h4>
                

                <p>Client : <?php echo $_GET['par'] ?></p>
                <p>Avenue Industrielle, Quartier Dilala</p>
                <p>Commune de Dilala, Kolwezi</p>
                <p>Tél : +243998045380</p>


            </div>


            <div class="invoice-details">

                <p><strong>N° Facture :</strong> FAC-2026-001</p>
                <p><strong>Date :</strong> <?php echo $row['datCom'] ?></p>
                <p><strong>Lieu :</strong> Kolwezi, Lualaba</p>

            </div>

        </div>

        <?php if (count($com) > 0) : ?>
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
                <?php foreach ($com as $cmd) : ?>
                <tr>
                    <td>1</td>
                    <td><?php echo $cmd['designP'].' '.$cmd['caractProduit'] ?></td>
                    <td>25 000</td>
                    <td><?php echo $cmd['Qte'].' '.$cmd['unitMes'] ?></td>
                    <td>125 000</td>
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


            <div class="total-box">

                <p>SOUS TOTAL : 197 000 CDF</p>
                <p>TVA : 0 %</p>

                <div class="grand-total">
                    TOTAL À PAYER : 197 000 CDF
                </div>

            </div>

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