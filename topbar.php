<?php
require_once __DIR__ . '/bd/database.php';

/* ===============================
   TOTAL ALERTES
================================= */

$sqlCount = "SELECT 
                COUNT(*) AS total
             FROM (
                SELECT p.idprod, p.seuil_min
                FROM produit p
                LEFT JOIN approvisionnement a ON p.idprod = a.idProd
                GROUP BY p.idprod
                HAVING COALESCE(SUM(a.Qte), 0) <= p.seuil_min
             ) AS t";

$stmtCount = $pdo->query($sqlCount);
$countAlert = $stmtCount->fetch()['total'];

/* ===============================
   ALERTES LIMITÉES
================================= */

$sqlAlert = "SELECT 
                p.idprod,
                p.designP,
                p.seuil_min,
                COALESCE(SUM(a.Qte), 0) AS stock
            FROM produit p
            LEFT JOIN approvisionnement a ON p.idprod = a.idProd
            GROUP BY p.idprod
            HAVING stock <= p.seuil_min
            ORDER BY p.idprod DESC
            LIMIT 5";

$stmtAlert = $pdo->query($sqlAlert);
$alertes = $stmtAlert->fetchAll();

?>     
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Rechercher..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Rechercher..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter">
                                    <?= ($countAlert > 9) ? '9+' : $countAlert ?>
                                </span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
     aria-labelledby="alertsDropdown">

    <h6 class="dropdown-header">
        Centre d'alertes
    </h6>

    <?php if ($countAlert > 0): ?>

        <?php foreach ($alertes as $a): ?>

            <?php 
                $stock = $a['stock'];
                $seuil = $a['seuil_min'];

                if ($stock == 0) {
                    $bg = 'bg-danger';
                    $message = "Rupture de stock";
                } else {
                    $bg = 'bg-warning';
                    $message = "Stock faible";
                }
            ?>

            <a class="dropdown-item d-flex align-items-center" href="/gestion_quincaillerie/produits/index.php">

                <div class="mr-3">
                    <div class="icon-circle <?= $bg ?>">
                        <i class="fas fa-exclamation-triangle text-white"></i>
                    </div>
                </div>

                <div>
                    <div class="small text-gray-500">
                        <?= date('d/m/Y H:i') ?>
                    </div>

                    <span class="font-weight-bold">
                        <?= htmlspecialchars($a['designP']) ?> - <?= $message ?>
                        (Stock: <?= $stock ?>)
                    </span>
                </div>

            </a>

        <?php endforeach; ?>

    <?php else: ?>

        <a class="dropdown-item text-center small text-gray-500" href="#">
            Aucune alerte
        </a>

    <?php endif; ?>

</div>
                        </li>
<!-- Nav Item - Messages -->
<li class="nav-item dropdown no-arrow mx-1">

    <a class="nav-link dropdown-toggle"
       href="#"
       id="messagesDropdown"
       role="button"
       data-toggle="dropdown">

        <i class="fas fa-envelope fa-fw"></i>

        <!-- Counter -->
        <span class="badge badge-danger badge-counter">
            3
        </span>

    </a>

    <!-- Dropdown -->
    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in">

        <h6 class="dropdown-header">
            Centre des messages
        </h6>

        <!-- Message 1 -->
        <a class="dropdown-item d-flex align-items-center" href="messages.php">
            <div class="dropdown-list-image mr-3">
                <img class="rounded-circle" src="img/undraw_profile.svg" alt="">
                <div class="status-indicator bg-success"></div>
            </div>
            <div class="font-weight-bold">
                <div class="text-truncate">
                    Nouvelle commande enregistrée avec succès...
                </div>
                <div class="small text-gray-500">
                    Système · 19 Mars 2026
                </div>
            </div>
        </a>

        <!-- Message 2 -->
        <a class="dropdown-item d-flex align-items-center" href="messages.php">
            <div class="dropdown-list-image mr-3">
                <img class="rounded-circle" src="img/undraw_profile.svg" alt="">
            </div>
            <div>
                <div class="text-truncate">
                    Stock faible pour certains produits...
                </div>
                <div class="small text-gray-500">
                    Magasin · 18 Mars 2026
                </div>
            </div>
        </a>

        <!-- Message 3 -->
        <a class="dropdown-item d-flex align-items-center" href="messages.php">
            <div class="dropdown-list-image mr-3">
                <img class="rounded-circle" src="img/undraw_profile.svg" alt="">
                <div class="status-indicator bg-success"></div>
            </div>
            <div class="font-weight-bold">
                <div class="text-truncate">
                    Nouveau client ajouté dans le système...
                </div>
                <div class="small text-gray-500">
                    Admin · 17 Mars 2026
                </div>
            </div>
        </a>

        <!-- Voir tous -->
        <a class="dropdown-item text-center small text-gray-500"
           href="messages.php">
            Voir tous les messages
        </a>

    </div>

</li>
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                               <span class="mr-2 d-none d-lg-inline text-gray-600 small">

                               <?php echo $_SESSION['prenom'].' '.$_SESSION['nom']; ?>

                                </span>
                                <img class="img-profile rounded-circle"
                                src="/gestion_quincaillerie/img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                             aria-labelledby="userDropdown">

                                <a class="dropdown-item" href="/gestion_quincaillerie/profile.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>

                               

                                <a class="dropdown-item" href="/gestion_quincaillerie/activity_log.php">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                   Historique des activités
                                </a>

                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item" href="/gestion_quincaillerie/logout.php">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>

                        </div>
                        </li>

                    </ul>

                </nav>
            