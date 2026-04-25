<?php
session_start();
require_once 'bd/database.php';

/* ===============================
   SÉCURITÉ : UTILISATEUR CONNECTÉ
================================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

/* ===============================
   NOMBRE DE COMMANDES (MOIS)
================================= */
$sqlNbCmd = "
    SELECT COUNT(*) AS total
    FROM commande
    WHERE MONTH(datCom) = MONTH(CURDATE())
      AND YEAR(datCom) = YEAR(CURDATE())
";
$stmtCmd = $pdo->query($sqlNbCmd);
$rowCmd = $stmtCmd->fetch(PDO::FETCH_ASSOC);
$nbCommandes = isset($rowCmd['total']) ? $rowCmd['total'] : 0;

/* ===============================
   NOMBRE DE SUCCURSALES
================================= */
$sqlNbSuc = "SELECT COUNT(*) AS total FROM succursale";
$stmtSuc = $pdo->query($sqlNbSuc);
$rowSuc = $stmtSuc->fetch(PDO::FETCH_ASSOC);
$nbSuccursales = isset($rowSuc['total']) ? $rowSuc['total'] : 0;


/* =====================================================
   CARTES – VENTES JOURNALIÈRES (USD / CDF)
===================================================== */
$qJour = $pdo->query("
    SELECT 
        SUM(CASE WHEN fp.unitMon = 'USD' THEN fp.pu * dc.Qte ELSE 0 END) AS total_usd,
        SUM(CASE WHEN fp.unitMon = 'CDF' THEN fp.pu * dc.Qte ELSE 0 END) AS total_cdf
    FROM commande c
    INNER JOIN detailscommande dc ON c.idCom = dc.idcom
    INNER JOIN approvisionnement a ON dc.idApprov = a.idAprov
    INNER JOIN fixationprix fp ON a.idAprov = fp.IdApprov
    WHERE DATE(c.datCom) = CURDATE()
");
$rowJour = $qJour->fetch(PDO::FETCH_ASSOC);

$ventesJourUSD = isset($rowJour['total_usd']) ? $rowJour['total_usd'] : 0;
$ventesJourCDF = isset($rowJour['total_cdf']) ? $rowJour['total_cdf'] : 0;


/* =====================================================
   CARTES – VENTES MENSUELLES (USD / CDF)
===================================================== */
$qMois = $pdo->query("
    SELECT 
        SUM(CASE WHEN fp.unitMon = 'USD' THEN fp.pu * dc.Qte ELSE 0 END) AS total_usd,
        SUM(CASE WHEN fp.unitMon = 'CDF' THEN fp.pu * dc.Qte ELSE 0 END) AS total_cdf
    FROM commande c
    INNER JOIN detailscommande dc ON c.idCom = dc.idcom
    INNER JOIN approvisionnement a ON dc.idApprov = a.idAprov
    INNER JOIN fixationprix fp ON a.idAprov = fp.IdApprov
    WHERE MONTH(c.datCom) = MONTH(CURDATE())
      AND YEAR(c.datCom) = YEAR(CURDATE())
");
$rowMois = $qMois->fetch(PDO::FETCH_ASSOC);

$ventesMoisUSD = isset($rowMois['total_usd']) ? $rowMois['total_usd'] : 0;
$ventesMoisCDF = isset($rowMois['total_cdf']) ? $rowMois['total_cdf'] : 0;


/* =====================================================
   GRAPHIQUE 1 – ÉVOLUTION DES VENTES (GLOBAL)
===================================================== */
$sqlVentesMois = "
    SELECT 
        MONTH(c.datCom) AS mois,
        SUM(fp.pu * dc.Qte) AS total
    FROM commande c
    INNER JOIN detailscommande dc ON c.idCom = dc.idcom
    INNER JOIN approvisionnement a ON dc.idApprov = a.idAprov
    INNER JOIN fixationprix fp ON a.idAprov = fp.IdApprov
    WHERE YEAR(c.datCom) = YEAR(CURDATE())
    GROUP BY MONTH(c.datCom)
    ORDER BY mois
";
$resVentes = $pdo->query($sqlVentesMois)->fetchAll(PDO::FETCH_ASSOC);

$labelsMois = array();
$dataVentes = array();
$moisNoms = array("Jan","Fév","Mar","Avr","Mai","Jun","Jul","Aoû","Sep","Oct","Nov","Déc");

foreach ($resVentes as $row) {
    $labelsMois[] = $moisNoms[$row['mois'] - 1];
    $dataVentes[] = $row['total'];
}


/* =====================================================
   GRAPHIQUE 2 – SOURCES DE REVENUS PAR SUCCURSALE
===================================================== */
$sqlSucc = "
    SELECT 
        s.nomSuc,
        SUM(fp.pu * dc.Qte) AS total
    FROM succursale s
    INNER JOIN commande c ON s.idsuc = c.idSuc
    INNER JOIN detailscommande dc ON c.idCom = dc.idcom
    INNER JOIN approvisionnement a ON dc.idApprov = a.idAprov
    INNER JOIN fixationprix fp ON a.idAprov = fp.IdApprov
    GROUP BY s.nomSuc
";
$resSucc = $pdo->query($sqlSucc)->fetchAll(PDO::FETCH_ASSOC);

$labelsSucc = array();
$dataSucc = array();

foreach ($resSucc as $row) {
    $labelsSucc[] = $row['nomSuc'];
    $dataSucc[]   = $row['total'];
}
?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>BISIKOMASH - Dashboard</title>
    <link rel="shortcut icon" href="/gestion_quincaillerie/img/icone.ico" type="image/x-icon">
    <link rel="icon" href="/gestion_quincaillerie/img/icone.ico" type="image/x-icon">

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <style>
@media print {

    @page {
        size: A4 landscape;
        margin: 10mm;
    }

    body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .sidebar,
    .topbar,
    .navbar,
    .btn,
    .scroll-to-top {
        display: none !important;
    }

    body * {
        visibility: hidden;
    }

    #print-area, #print-area * {
        visibility: visible;
    }

    #print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

}
</style>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

       <!-- MENU -->

       <?php include("menu.php"); ?>

        <!-- FIN MENU -->


        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
    <div id="content">
        <div id="print-area">

                 <!-- Topbar -->

                 <?php include("topbar.php"); ?>

            <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Tableau de bord</h1>
                       <a href="#" onclick="printDashboard()"
                           class="btn btn-primary">
                           <i class="fas fa-print"></i> Imprimer
                        </a>
                     </div>

                    <!-- Content Row -->
        <div class="row">

                       <!-- Ventes journalières -->
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">

                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Ventes journalières
                    </div>

                    <!-- USD -->
                    <div class="h5 mb-1 font-weight-bold text-gray-800">
                        <?= number_format($ventesJourUSD, 2, ',', ' ') ?> USD
                    </div>

                    <!-- CDF -->
                    <div class="text-sm text-muted">
                        <?= number_format($ventesJourCDF, 0, ',', ' ') ?> CDF
                    </div>

                </div>
                <div class="col-auto">
                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ventes mensuelles -->
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">

                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Ventes mensuelles
                    </div>

                    <!-- USD -->
                    <div class="h5 mb-1 font-weight-bold text-gray-800">
                        <?= number_format($ventesMoisUSD, 2, ',', ' ') ?> USD
                    </div>

                    <!-- CDF -->
                    <div class="text-sm text-muted">
                        <?= number_format($ventesMoisCDF, 0, ',', ' ') ?> CDF
                    </div>

                </div>
                <div class="col-auto">
                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>

                       <!-- Nombre des commandes mensuelles -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">

                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Nombre des commandes mensuelles
                                    </div>

                                    <div class="row no-gutters align-items-center">
                                        <!-- Valeur -->
                                        <div class="col-auto">
                                            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                                <?= $nbCommandes ?>
                                            </div>
                                        </div>

                                        <!-- Barre de progression -->
                                        <div class="col">
                                            <div class="progress progress-sm mr-2">
                                                <div class="progress-bar bg-info"
                                                     role="progressbar"
                                                     style="width: <?= min($nbCommandes, 100) ?>%"
                                                     aria-valuenow="<?= $nbCommandes ?>"
                                                     aria-valuemin="0"
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Icône -->
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                       <!-- Nombre de succursales -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">

                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Nombre de succursales
                                        </div>

                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?= $nbSuccursales ?>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <i class="fas fa-building fa-2x text-gray-300"></i>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    </div>

                    <!-- Content Row -->

                    <div class="row">

                        <!-- Area Chart -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Aperçu des résultats</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Dropdown Header:</div>
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="myAreaChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pie Chart -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Sources de revenus</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Dropdown Header:</div>
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2">
                                        <canvas id="myPieChart"></canvas>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                    
                <div class="row">  
                       
                </div>
                

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

      <!-- PIED DE PAGE -->

       <?php include("pieds.php"); ?>

        <!-- FIN PIED DE PAGE -->
        </div>
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


    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts 
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>-->

<script>
const areaCtx = document.getElementById("myAreaChart").getContext("2d");

new Chart(areaCtx, {
    type: "line",
    data: {
        labels: <?= json_encode($labelsMois) ?>,
        datasets: [{
            label: "Ventes mensuelles",
            data: <?= json_encode($dataVentes) ?>,
            borderColor: "#4e73df",
            backgroundColor: "rgba(78, 115, 223, 0.15)",
            tension: 0.4,
            fill: true,

            /* ✅ Mettre en valeur un seul point */
            pointRadius: 6,
            pointBackgroundColor: "#4e73df",
            pointHoverRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,

        plugins: {
            legend: { display: false }
        },

        scales: {
            y: {
                beginAtZero: true,   // ✅ essentiel
                grace: "15%",       // ✅ espace au-dessus du point

                ticks: {
                    callback: function (value) {
                        return value.toLocaleString(); // format lisible
                    }
                }
            },
            x: {
                ticks: {
                    maxRotation: 0
                }
            }
        }
    }
});
</script>


<script>
const pieCtx = document.getElementById("myPieChart").getContext("2d");

new Chart(pieCtx, {
    type: "doughnut",
    data: {
        labels: <?= json_encode($labelsSucc) ?>,
        datasets: [{
            data: <?= json_encode($dataSucc) ?>,
            backgroundColor: [
                "#4e73df",
                "#1cc88a",
                "#36b9cc",
                "#f6c23e",
                "#e74a3b"
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: "bottom"
            }
        }
    }
});
</script>


<script>
function printDashboard() {

    // récupérer les charts
    let charts = document.querySelectorAll("canvas");

    charts.forEach((canvas) => {
        let img = document.createElement("img");
        img.src = canvas.toDataURL("image/png");
        img.style.width = canvas.style.width;
        img.style.height = canvas.style.height;

        canvas.parentNode.replaceChild(img, canvas);
    });

    window.print();

    // recharger la page après impression (important)
    setTimeout(() => {
        location.reload();
    }, 1000);
}
</script>
<script>
function printDashboard() {
    window.print();
}
</script>

</body>

</html>