<?php

session_start();

require_once("bd/database.php");
require_once("auth_admin.php");

if(!isset($_SESSION['user_id']))
{
    header("Location: index.php");
    exit();
}

/* ===============================
   Pagination
================================= */

$limit = 25;

/* page actuelle */

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

/* calcul offset */

$offset = ($page - 1) * $limit;

/* ===============================
   Nombre total d'activités
================================= */

$total_sql = "SELECT COUNT(*) FROM activity_log";
$total_stmt = $pdo->query($total_sql);
$total_rows = $total_stmt->fetchColumn();

$total_pages = ceil($total_rows / $limit);

/* ===============================
   Récupération des activités
================================= */

$sql = "SELECT a.*, u.nom, u.prenom
        FROM activity_log a
        LEFT JOIN utilisateur u
        ON a.user_id = u.idutil
        ORDER BY a.date_action DESC
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>BISIKOMASH - Activity Log</title>

    <link rel="shortcut icon" href="/gestion_quincaillerie/img/icone.ico" type="image/x-icon">
    <link rel="icon" href="/gestion_quincaillerie/img/icone.ico" type="image/x-icon">

    <!-- Fonts -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- SB Admin CSS -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

<div id="wrapper">

    <!-- MENU -->
    <?php include("menu.php"); ?>
    <!-- FIN MENU -->


    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <!-- TOPBAR -->
            <?php include("topbar.php"); ?>
            <!-- FIN TOPBAR -->

<!-- Page Content -->
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">
        Historique des activités
    </h1>

    <div class="card shadow mb-4">

        <form method="POST" action="delete_activity.php">

            <!-- Card Header -->
            <div class="card-header py-3 d-flex justify-content-between align-items-center">

                <h6 class="m-0 font-weight-bold text-primary">
                    Historique des activités
                </h6>

                <button type="submit"
                            id="deleteBtn"
                            class="btn btn-danger btn-sm"
                            disabled
                            onclick="return confirm('Supprimer les activités sélectionnées ?')">

                        <i class="fas fa-trash"></i>
                        Supprimer la sélection

                    </button>

            </div>


            <!-- Card Body -->
            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered">

                        <thead>

                            <tr>

                                <th width="40">
                                    <input type="checkbox" id="checkAll">
                                </th>

                                <th>Utilisateur</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>Date</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>

                                <tr>

                                    <td>
                                        <input type="checkbox"
                                               name="ids[]"
                                               value="<?php echo $row['id']; ?>">
                                    </td>

                                    <td>
                                        <?php echo $row['prenom'] . " " . $row['nom']; ?>
                                    </td>

                                    <td>
                                        <?php echo $row['action']; ?>
                                    </td>

                                    <td>
                                        <?php echo $row['description']; ?>
                                    </td>

                                    <td>
                                        <?php echo $row['date_action']; ?>
                                    </td>

                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </form>


        <!-- Pagination -->
        <div class="card-footer">

            <nav>

                <ul class="pagination justify-content-center">

                    <?php if ($page > 1) { ?>

                        <li class="page-item">

                            <a class="page-link"
                               href="?page=<?php echo $page - 1; ?>">

                                Précédent

                            </a>

                        </li>

                    <?php } ?>


                    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>

                        <li class="page-item <?php if ($i == $page) echo 'active'; ?>">

                            <a class="page-link"
                               href="?page=<?php echo $i; ?>">

                                <?php echo $i; ?>

                            </a>

                        </li>

                    <?php } ?>


                    <?php if ($page < $total_pages) { ?>

                        <li class="page-item">

                            <a class="page-link"
                               href="?page=<?php echo $page + 1; ?>">

                                Suivant

                            </a>

                        </li>

                    <?php } ?>

                </ul>

            </nav>

        </div>

    </div>

</div>
            <!-- /.container-fluid -->

        </div>


        <!-- FOOTER -->
        <?php include("pieds.php"); ?>
        <!-- FIN FOOTER -->


    </div>

</div>


<!-- Scroll Top -->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>


<!-- Logout Modal -->
<div class="modal fade"
     id="logoutModal"
     tabindex="-1"
     role="dialog">

    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Ready to Leave?
                </h5>

                <button class="close"
                        type="button"
                        data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <div class="modal-body">
                Select "Logout" below if you are ready to end your session.
            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary"
                        type="button"
                        data-dismiss="modal">

                    Cancel

                </button>

                <a class="btn btn-primary"
                   href="logout.php">

                    Logout

                </a>

            </div>

        </div>

    </div>

</div>



<!-- JS -->
<script src="vendor/jquery/jquery.min.js"></script>

<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<script src="js/sb-admin-2.min.js"></script>

<script>

    document.getElementById('checkAll').addEventListener('click', function () {

        let checkboxes = document.querySelectorAll('input[name="ids[]"]');

        checkboxes.forEach(function (checkbox) {

            checkbox.checked = document.getElementById('checkAll').checked;

        });

    });

</script>

<script>

    const checkAll  = document.getElementById("checkAll");
    const checkboxes = document.querySelectorAll('input[name="ids[]"]');
    const deleteBtn = document.getElementById("deleteBtn");


    function toggleDeleteButton() {

        let checked = document.querySelectorAll('input[name="ids[]"]:checked');

        deleteBtn.disabled = checked.length === 0;

    }


    checkboxes.forEach(function (checkbox) {

        checkbox.addEventListener("change", toggleDeleteButton);

    });


    checkAll.addEventListener("change", function () {

        checkboxes.forEach(function (checkbox) {

            checkbox.checked = checkAll.checked;

        });

        toggleDeleteButton();

    });

</script>


</body>
</html>