<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Brand -->
   <a href="/gestion_quincaillerie/dashboard.php" 
   class="sidebar-brand" 
   style="padding:0 !important; height:auto !important; min-height:unset !important;">

    <img src="/gestion_quincaillerie/img/bisikomashLogo1.PNG" 
         alt="Bisikomash Logo"
         style="width:100%; height:90px; object-fit:contain; display:block;">

</a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="/gestion_quincaillerie/dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

 <!-- ================= ACHAT ================= -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAchat">
        <i class="fas fa-truck"></i>
        <span>Achat</span>
    </a>
    <div id="collapseAchat" class="collapse" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">

            <a class="collapse-item" href="/gestion_quincaillerie/fournisseurs/index.php">
                <i class="fas fa-industry mr-2"></i> Fournisseurs
            </a>

            <a class="collapse-item" href="/gestion_quincaillerie/approvisionnement/index.php">
                <i class="fas fa-warehouse mr-2"></i> Stockage
            </a>

        </div>
    </div>
</li>


<!-- ================= VENTE ================= -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseVente">
        <i class="fas fa-cash-register"></i>
        <span>Vente</span>
    </a>
    <div id="collapseVente" class="collapse" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">

            <a class="collapse-item" href="/gestion_quincaillerie/commandes/index.php">

                <i class="fas fa-shopping-cart mr-2"></i> Commande
            </a>

            <a class="collapse-item" href="paiements/index.php">
                <i class="fas fa-file-invoice-dollar mr-2"></i> Facturation
            </a>
            <a class="collapse-item" href="/gestion_quincaillerie/clients/index.php">
                 <i class="fas fa-user-friends"></i> Clients
            </a>

        </div>
    </div>
</li>

    <!-- ================= STOCK ================= -->
    <!-- STOCK -->
    <div class="sidebar-heading">Stock</div>

   
    <li class="nav-item">
        <a class="nav-link" href="stock/etat_stock.php">
            <i class="fas fa-warehouse"></i>
            <span>État du stock</span>
        </a>
    </li>

    <hr class="sidebar-divider">
    <!-- ================= RAPPORTS ================= -->
    <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRapports">
        <i class="fas fa-chart-line"></i>
        <span>Rapports</span>
    </a>

    <div id="collapseRapports" class="collapse" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">

            <a class="collapse-item" href="rapports/ventes.php">
                <i class="fas fa-chart-bar mr-2 text-primary"></i>
                Rapport ventes
            </a>

            <a class="collapse-item" href="rapports/achats.php">
                <i class="fas fa-file-invoice mr-2 text-success"></i>
                Rapport achats
            </a>

            <a class="collapse-item" href="rapports/benefices.php">
                <i class="fas fa-coins mr-2 text-warning"></i>
                Rapport bénéfices
            </a>

        </div>
    </div>
</li>
  <hr class="sidebar-divider">

    <!-- ================= ADMINISTRATION ================= -->
    <!-- ADMINISTRATION -->
    <div class="sidebar-heading">Administration</div>

    <li class="nav-item">
        <a class="nav-link" href="/gestion_quincaillerie/utilisateurs/index.php">
            <i class="fas fa-user-shield"></i>
            <span>Utilisateurs</span>
        </a>
    </li>
    
     <li class="nav-item">
    <a class="nav-link" href="/gestion_quincaillerie/produits/index.php">
        <i class="fas fa-boxes"></i>
        <span>Produits</span>
    </a>
</li>

    <li class="nav-item">
        <a class="nav-link" href="/gestion_quincaillerie/succursales/index.php">
            <i class="fas fa-building"></i>
            <span>Succursales</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <!-- Toggle -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>


   
</ul>
<!-- End of Sidebar -->