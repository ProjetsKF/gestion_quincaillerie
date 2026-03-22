<?php

require_once '../bd/database.php';
require_once '../auth_admin.php';

/* ===============================
   PAGINATION
================================= */

$limit = 10;

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;

/* ===============================
   RECHERCHE
================================= */

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

/* ===============================
   COMPTER LES RÉSULTATS
================================= */

$sqlCount = "SELECT COUNT(*) 
             FROM approvisionnement a
             INNER JOIN produit p ON a.idProd = p.idprod
             INNER JOIN fournisseur f ON a.idFourn = f.id";

if (!empty($search)) {
    $sqlCount .= " WHERE p.designP LIKE :search
                   OR f.nom LIKE :search
                   OR f.postnom LIKE :search";
}

$stmtCount = $pdo->prepare($sqlCount);

if (!empty($search)) {
    $stmtCount->bindValue(':search', "%$search%", PDO::PARAM_STR);
}

$stmtCount->execute();
$totalRows = $stmtCount->fetchColumn();

$totalPages = ceil($totalRows / $limit);

/* ===============================
   REQUÊTE PRINCIPALE
================================= */

$sql = "

SELECT 
    a.idAprov,
    a.Qte,
    a.pu AS prixAchat,
    a.datAprov,
    a.unitMon as unitMonAch,

    p.designP,

    f.nom,
    f.postnom,

    s.nomSuc,

    fp.pu AS prixVente,
    fp.unitMon,
    fp.idfix,

    (fp.pu - a.pu) AS marge

FROM approvisionnement a

INNER JOIN produit p ON a.idProd = p.idprod
INNER JOIN fournisseur f ON a.idFourn = f.id
INNER JOIN succursale s ON a.idSuc = s.idsuc
LEFT JOIN fixationprix fp ON a.idAprov = fp.IdApprov

";

if (!empty($search)) {
    $sql .= " WHERE p.designP LIKE :search
              OR f.nom LIKE :search
              OR f.postnom LIKE :search ";
}

$sql .= " ORDER BY a.idAprov DESC
          LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);

if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

$appros = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>BISIKOMASH - Fixation de prix</title>
<link rel="shortcut icon" href="/gestion_quincaillerie/img/icone.ico" type="image/x-icon">
 <link rel="icon" href="/gestion_quincaillerie/img/icone.ico" type="image/x-icon">

<link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
<link href="../css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

<div id="wrapper">

<?php include("../menu.php"); ?>

<div id="content-wrapper" class="d-flex flex-column">

<div id="content">

<?php include("../topbar.php"); ?>


<div class="container-fluid">

<div class="mb-3">

<a href="/gestion_quincaillerie/dashboard.php" class="text-secondary">

<i class="fas fa-arrow-left"></i>
Retour au tableau de bord

</a>

</div>


<div class="card shadow mb-4">

<div class="card-header py-3">

<div class="d-flex justify-content-between align-items-center">

<h6 class="m-0 font-weight-bold text-primary">

Fixation des prix de vente

</h6>

<a href="index.php" class="btn btn-outline-secondary btn-sm">

<i class="fas fa-sync-alt"></i>
Actualiser

</a>

</div>

</div>


<div class="card-body">

	<div class="d-flex justify-content-between align-items-center mb-3">

<div>
Affichage de <?php echo count($appros); ?> éléments
</div>

<nav>

<ul class="pagination pagination-sm mb-0">

<?php if($page > 1): ?>

<li class="page-item">

<a class="page-link" href="?page=<?php echo $page-1; ?>">
Précédent
</a>

</li>

<?php endif; ?>


<?php for($i = 1; $i <= $totalPages; $i++): ?>

<li class="page-item <?php if($i == $page) echo 'active'; ?>">

<a class="page-link" href="?page=<?php echo $i; ?>">
<?php echo $i; ?>
</a>

</li>

<?php endfor; ?>


<?php if($page < $totalPages): ?>

<li class="page-item">

<a class="page-link" href="?page=<?php echo $page+1; ?>">
Suivant
</a>

</li>

<?php endif; ?>

</ul>

</nav>

</div>

<div class="table-responsive">


	<form method="GET" class="mb-3">
    <div class="input-group">
        <input type="text" 
               name="search" 
               class="form-control"
               placeholder="Rechercher un produit..."
               value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">

        <div class="input-group-append">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
</form>

<table class="table table-hover table-sm">

<thead class="thead-light">

<tr>

<th>ID</th>
<th>Produit</th>
<th>Fournisseur</th>
<th>Quantité</th>
<th>Prix Achat</th>
<th>Prix Vente</th>
<th>Marge</th>
<th>Date Appro</th>
<th>Succursale</th>
<th class="text-center">Action</th>

</tr>

</thead>

<tbody>

<?php foreach($appros as $a): ?>

<tr>

<td><?php echo $a['idAprov']; ?></td>

				<td>
				<i class="fas fa-box text-muted mr-1"></i>
				<?php echo $a['designP']; ?>
				</td>

				<td>
				<i class="fas fa-user text-muted mr-1"></i>
				<?php echo $a['nom']." ".$a['postnom']; ?>
				</td>

				<td><?php echo $a['Qte']; ?></td>

				<td><?php echo number_format($a['prixAchat'],0,","," ").$a['unitMonAch']; ?> </td>
				<td>

				<?php if($a['prixVente']): ?>

				<span class="badge badge-success">

				<?php echo number_format($a['prixVente'],0,","," "); ?>
				<?php echo $a['unitMon']; ?>

				</span>

				<?php else: ?>

				<span class="badge badge-secondary">

				Non fixé

				</span>

				<?php endif; ?>

				</td>

			<td>

		<?php if($a['prixVente']) : ?>

		<span class="text-success font-weight-bold">

		<?php echo number_format($a['marge'],0,","," "); ?>
		<?php echo $a['unitMon']; ?>

		</span>

		<?php else : ?>

		<span class="text-muted">
		-
		</span>

		<?php endif; ?>

		</td>

<td><?php echo $a['datAprov']; ?></td>


<td>

<i class="fas fa-building text-muted mr-1"></i>
<?php echo $a['nomSuc']; ?>

</td>

	<td class="text-center">

	<?php if($a['prixVente'] == NULL): ?>

	<button 
	        class="btn btn-primary btn-sm"
	        data-toggle="modal"
	        data-target="#prixModal<?php echo $a['idAprov']; ?>"
	>

	<i class="fas fa-tags"></i>
	Fixer prix

	</button>

	<?php else: ?>

	<button 
	        class="btn btn-warning btn-sm"
	        data-toggle="modal"
	        data-target="#prixModal<?php echo $a['idAprov']; ?>"
	>

	<i class="fas fa-edit"></i>
	Modifier prix

	</button>

	<?php endif; ?>

	</td>

</tr>


<!-- MODAL FIXATION PRIX -->

<div class="modal fade"
id="prixModal<?php echo $a['idAprov']; ?>"
tabindex="-1">

<div class="modal-dialog modal-dialog-centered">

<div class="modal-content">

<div class="modal-header">

<h5 class="modal-title">

<i class="fas fa-tags text-primary"></i>
Fixation du prix

</h5>

<button class="close" data-dismiss="modal">
<span>&times;</span>
</button>

</div>

<form method="POST" action="save_prix.php">

<input type="hidden"
name="idAprov"
value="<?php echo $a['idAprov']; ?>">

<div class="modal-body">

<div class="form-group">

<label>Produit</label>

<input type="text"
class="form-control"
value="<?php echo $a['designP']; ?>"
readonly>

</div>


<div class="form-group">

<label>Prix d'achat</label>

<input type="text"
class="form-control"
value="<?php echo number_format($a['prixAchat'],0,","," ").' '.$a['unitMonAch']; ?> "
readonly>

</div>


<div class="form-group">

<label>Prix de vente *</label>

<input type="number"
name="pu"
class="form-control"
placeholder="Entrer le prix de vente"
required>

</div>

</div>

<div class="modal-footer">

<button type="submit" class="btn btn-success">

<i class="fas fa-save"></i>
Enregistrer

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

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</div>

<?php include("../pieds.php"); ?>

</div>

</div>


<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>

</body>

</html>