<?php

session_start();

require_once '../bd/database.php';

/* ===============================
   PAGINATION PRODUITS
================================= */

/* Nombre de produits par page */
$limit = 10;

/* Page actuelle */
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

/* Calcul OFFSET */
$offset = ($page - 1) * $limit;

/* ===============================
   TOTAL PRODUITS (SANS GROUP BY)
================================= */
$countQuery = $pdo->query("SELECT COUNT(*) FROM produit");
$totalProducts = $countQuery->fetchColumn();

/* Nombre total de pages */
$totalPages = ceil($totalProducts / $limit);

/* ===============================
   REQUÊTE PRODUITS + STOCK
================================= */

$sql = "SELECT 
            p.idprod,
            p.designP,
            p.caractProduit,
            p.seuil_min,
            COALESCE(SUM(a.Qte), 0) AS total_appro
        FROM produit p
        LEFT JOIN approvisionnement a ON p.idprod = a.idProd
        GROUP BY p.idprod
        ORDER BY p.idprod DESC
        LIMIT :limit OFFSET :offset";

$res = $pdo->prepare($sql);

$res->bindValue(':limit', $limit, PDO::PARAM_INT);
$res->bindValue(':offset', $offset, PDO::PARAM_INT);

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
                Gestion des Produits
            </h6>

<!-- Bouton Actualiser -->
       
            <button class="btn btn-primary btn-sm"
                    data-toggle="modal"
                    data-target="#produitModal">
                <i class="fas fa-plus"></i> Nouveau Produit
            </button>
        </div>

        <!-- Body -->
         <!-- Barre de recherche -->
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

           
            
            <!-- MESSAGE DE CONFIRMATION -->

            <?php if (isset($_GET['deleted'])): ?>

                <div class="alert alert-success alert-dismissible fade show">

                    <i class="fas fa-check-circle"></i>
                    Produit supprimé avec succès.

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


                <?php if (isset($_GET['updated'])): ?>

                    <div class="alert alert-success alert-dismissible fade show">

                        <i class="fas fa-check-circle"></i>
                        Produit modifié avec succès.

                        <button type="button"
                                class="close"
                                data-dismiss="alert">
                            <span>&times;</span>
                        </button>

                    </div>

                    <script>

                    if (window.location.search.includes("updated")) {

                        const url = new URL(window.location);

                        url.searchParams.delete("updated");

                        window.history.replaceState({}, document.title, url.pathname);

                    }

                    </script>

                    <?php endif; ?>

                        <?php if (isset($_GET['added'])): ?>

                        <div class="alert alert-success alert-dismissible fade show">

                            <i class="fas fa-check-circle"></i>
                            Produit ajouté avec succès.

                            <button type="button"
                                    class="close"
                                    data-dismiss="alert">
                                <span>&times;</span>
                            </button>

                        </div>

                        <script>

                        if (window.location.search.includes("added")) {

                            const url = new URL(window.location);

                            url.searchParams.delete("added");

                            window.history.replaceState({}, document.title, url.pathname);

                        }

                        </script>

                        <?php endif; ?>

            <!-- Tableau -->
            <div class="table-responsive">
                <?php if (count($prod) > 0) : ?>
                <table class="table table-hover align-items-center">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Désignation</th>
                            <th>Caractéristiques</th>
                            <th>Stock</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                  <tbody>

<?php foreach ($prod as $pr) : 

    $stock = $pr['total_appro'];
    $seuil = $pr['seuil_min'];

    // Détermination du statut + couleur ligne
    if ($stock == 0) {
        $status = '<span class="badge badge-danger">Rupture</span>';
        $rowClass = 'table-danger';
    } elseif ($stock <= $seuil) {
        $status = '<span class="badge badge-warning">Faible</span>';
        $rowClass = 'table-warning';
    } else {
        $status = '<span class="badge badge-success">OK</span>';
        $rowClass = '';
    }

?>

<tr class="<?= $rowClass ?>">

    <td><?= htmlspecialchars($pr['idprod']) ?></td>
    <td><?= htmlspecialchars($pr['designP']) ?></td>
    <td><?= htmlspecialchars($pr['caractProduit']) ?></td>

    <!-- STOCK -->
    <td><?= htmlspecialchars($stock) ?></td>

    <!-- STATUT -->
    <td><?= $status ?></td>

    <!-- ACTIONS -->
    <td class="text-center">

        <!-- Modifier -->
      <a href="#"
               class="text-primary mr-3 btn-edit"
               data-toggle="modal"
               data-target="#editProduitModal"
               data-id="<?= $pr['idprod'] ?>"
               data-design="<?= htmlspecialchars($pr['designP']) ?>"
               data-caract="<?= htmlspecialchars($pr['caractProduit']) ?>"
               data-seuil="<?= $pr['seuil_min'] ?>"
               title="Modifier">

            <i class="fas fa-edit"></i>

        </a>

        <!-- Supprimer -->
        <a href="../produits/deleteProduit.php?id=<?= $pr['idprod'] ?>"
           class="text-danger"
           title="Supprimer"
           onclick="return confirm('Supprimer ce produit ?');">

            <i class="fas fa-trash"></i>

        </a>

    </td>

    </tr>

    <?php endforeach; ?>

    </tbody>

</table>

<?php endif; ?>
            </div>

            <div class="d-flex justify-content-center mt-3">

<nav>

<ul class="pagination pagination-sm">

<?php if ($page > 1): ?>

<li class="page-item">
<a class="page-link" href="?page=<?php echo $page - 1; ?>">
Précédent
</a>
</li>

<?php endif; ?>


<?php for ($i = 1; $i <= $totalPages; $i++): ?>

<li class="page-item <?php if ($i == $page) echo 'active'; ?>">

<a class="page-link" href="?page=<?php echo $i; ?>">
<?php echo $i; ?>
</a>

</li>

<?php endfor; ?>


<?php if ($page < $totalPages): ?>

<li class="page-item">
<a class="page-link" href="?page=<?php echo $page + 1; ?>">
Suivant
</a>
</li>

<?php endif; ?>

</ul>

</nav>

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

                <!-- FORMULAIRE CORRECT -->
                <form method="post" action="create.php">

                    <div class="form-group">
                        <label>Désignation *</label>
                        <input type="text" class="form-control" name="designP" 
                               placeholder="Ex: Clou 3 pouces" required>
                    </div>

                    <div class="form-group">
                        <label>Caractéristiques</label>
                        <textarea class="form-control"
                                  rows="3"
                                  placeholder="Ex: Acier galvanisé, 3 pouces" 
                                  name="caractProduit"></textarea>
                    </div>

                    <!-- ✅ NOUVEAU CHAMP SEUIL MIN -->
                    <div class="form-group">
                        <label>Seuil minimum *</label>
                        <input type="number" 
                               class="form-control" 
                               name="seuil_min"
                               min="0"
                               value="5"
                               required>
                        <small class="form-text text-muted">
                            Stock minimum avant alerte
                        </small>
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

<!-- Modal Modifier Produit -->
<div class="modal fade" id="editProduitModal" tabindex="-1">

    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> Modifier Produit
                </h5>

                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>

            </div>

            <form method="POST" action="updateProduit.php">

                <div class="modal-body">

                    <input type="hidden" name="idprod" id="edit_idprod">

                    <div class="form-group">
                        <label>Désignation *</label>
                        <input type="text"
                               class="form-control"
                               name="designP"
                               id="edit_designP"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Caractéristiques</label>
                        <textarea class="form-control"
                                  name="caractProduit"
                                  id="edit_caractProduit"
                                  rows="3"></textarea>
                    </div>

                    <!-- ✅ NOUVEAU CHAMP -->
                    <div class="form-group">
                        <label>Seuil minimum *</label>
                        <input type="number"
                               class="form-control"
                               name="seuil_min"
                               id="edit_seuil_min"
                               min="0"
                               required>
                    </div>

                </div>

                <div class="modal-footer">

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
   <script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>

<script>

document.querySelectorAll('.btn-edit').forEach(button => {

    button.addEventListener('click', function(){

        const id = this.getAttribute('data-id');
        const design = this.getAttribute('data-design');
        const caract = this.getAttribute('data-caract');
        const seuil = this.getAttribute('data-seuil');

        document.getElementById('edit_idprod').value = id;
        document.getElementById('edit_designP').value = design;
        document.getElementById('edit_caractProduit').value = caract;
        document.getElementById('edit_seuil_min').value = seuil;

    });

});

</script>

</body>

</html>