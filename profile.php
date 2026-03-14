<?php

session_start();

require_once("bd/database.php");

if(!isset($_SESSION['user_id']))
{
    header("Location: /gestion_quincaillerie/index.php");
    exit();
}

$id = $_SESSION['user_id'];

$sql = "SELECT u.*, s.nomSuc
        FROM utilisateur u
        LEFT JOIN succursale s
        ON u.idSuc = s.idsuc
        WHERE u.idutil = ?";

$stmt = $pdo->prepare($sql);

$stmt->execute([$id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>BISIKOMASH - Profile</title>

    <link rel="shortcut icon" href="/gestion_quincaillerie/img/icone.ico" type="image/x-icon">
    <link rel="icon" href="/gestion_quincaillerie/img/icone.ico" type="image/x-icon">

    <!-- Fonts -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- Styles -->
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

            <!-- PAGE CONTENT -->
            <div class="container-fluid">

                <h1 class="h3 mb-4 text-gray-800">
                    User Profile
                </h1>

        <!-- MESSAGES-->

         <?php if(isset($_GET['error'])){ ?>

                <?php if($_GET['error'] == "wrongpassword"){ ?>

                    <div class="alert alert-danger">
                        Mot de passe actuel incorrect.
                    </div>

                <?php } ?>

                <?php if($_GET['error'] == "nomatch"){ ?>

                    <div class="alert alert-warning">
                        Les mots de passe ne correspondent pas.
                    </div>

                <?php } ?>

            <?php } ?>


            <?php if(isset($_GET['success']) && $_GET['success'] == "passwordchanged"){ ?>

                <div class="alert alert-success">
                    Mot de passe modifié avec succès.
                </div>

            <?php } ?>


        <div class="row">
 
    <!-- Profile Card -->
    <div class="col-xl-4 col-lg-4">

        <div class="card shadow mb-4">

            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Profile Information
                </h6>
            </div>

            <div class="card-body text-center">

                <!-- Photo utilisateur -->
                <img class="img-profile rounded-circle mb-3"
                     src="/gestion_quincaillerie/img/undraw_profile.svg"
                     width="140">

                <!-- Nom complet -->
                <h5 class="font-weight-bold text-gray-800">
                    <?php echo $user['prenom']." ".$user['nom']; ?>
                </h5>

                <!-- Email -->
                <p class="text-muted mb-2">
                    <?php echo $user['email']; ?>
                </p>

               <!-- Role -->
                <?php if($user['rol'] == 1){ ?>

                    <span class="badge badge-danger">
                        Administrateur
                    </span>

                <?php } else { ?>

                    <span class="badge badge-info">
                        Utilisateur
                    </span>

                <?php } ?>

                <!-- Statut -->
                <span class="badge badge-success ml-2">
                    <?php echo $user['statut']; ?>
                </span>

                <hr>

                <!-- Boutons -->
                <a href="#"
                   class="btn btn-primary btn-sm"
                   data-toggle="modal"
                   data-target="#editProfileModal">

                    <i class="fas fa-user-edit"></i>
                    Edit Profile

                </a>

               <a href="#"
               class="btn btn-warning btn-sm"
               data-toggle="modal"
               data-target="#changePasswordModal">

                <i class="fas fa-key"></i>
                Change Password

            </a>

            </div>

        </div>

    </div>


    <!-- User Information -->
    <div class="col-xl-8 col-lg-8">

        <div class="card shadow mb-4">

            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    User Information
                </h6>
            </div>

            <div class="card-body">

                <table class="table table-bordered">

                    <tr>
                        <th>Nom</th>
                        <td><?php echo $user['nom']; ?></td>
                    </tr>

                    <tr>
                        <th>Prénom</th>
                        <td><?php echo $user['prenom']; ?></td>
                    </tr>

                    <tr>
                        <th>Email</th>
                        <td><?php echo $user['email']; ?></td>
                    </tr>

                    <tr>
                        <th>Rôle</th>
                        <td>

                            <?php if($user['rol'] == 1){ ?>

                                <span class="badge badge-danger">
                                    Administrateur
                                </span>

                            <?php } else { ?>

                                <span class="badge badge-info">
                                    Utilisateur
                                </span>

                            <?php } ?>

                            </td>
                    </tr>

                    <tr>
                        <th>Statut</th>
                        <td>
                            <span class="badge badge-success">
                                <?php echo $user['statut']; ?>
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>Succursale</th>
                        <td>
                           <?php echo $user['nomSuc']; ?>
                        </td>
                    </tr>

                </table>

            </div>

        </div>

    </div>

</div>

            </div>
            <!-- FIN PAGE CONTENT -->

        </div>

        <!-- FOOTER -->
        <?php include("pieds.php"); ?>
        <!-- FIN FOOTER -->

    </div>

</div>

<!-- Scroll to Top -->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Edit Profile Modal -->
<div class="modal fade"
     id="editProfileModal"
     tabindex="-1"
     role="dialog"
     aria-labelledby="editProfileModalLabel"
     aria-hidden="true">

    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <form method="POST" action="/gestion_quincaillerie/utilisateurs/update_profile.php">

                <div class="modal-header">

                    <h5 class="modal-title"
                        id="editProfileModalLabel">

                        Modifier le profil

                    </h5>

                    <button class="close"
                            type="button"
                            data-dismiss="modal">

                        <span>&times;</span>

                    </button>

                </div>


                <div class="modal-body">

                    <!-- Nom -->
                    <div class="form-group">

                        <label>Nom</label>

                        <input type="text"
                               name="nom"
                               class="form-control"
                               value="<?php echo $user['nom']; ?>"
                               required>

                    </div>


                    <!-- Prénom -->
                    <div class="form-group">

                        <label>Prénom</label>

                        <input type="text"
                               name="prenom"
                               class="form-control"
                               value="<?php echo $user['prenom']; ?>"
                               required>

                    </div>


                    <!-- Email -->
                    <div class="form-group">

                        <label>Email</label>

                        <input type="email"
                               name="email"
                               class="form-control"
                               value="<?php echo $user['email']; ?>"
                               required>

                    </div>

                </div>


                <div class="modal-footer">

                    <button class="btn btn-secondary"
                            type="button"
                            data-dismiss="modal">

                        Annuler

                    </button>

                    <button class="btn btn-primary"
                            type="submit">

                        Enregistrer

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- Change Password Modal -->
<div class="modal fade"
     id="changePasswordModal"
     tabindex="-1"
     role="dialog">

    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <form method="POST"
                  action="utilisateurs/update_password.php" autocomplete="off">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Changer le mot de passe
                    </h5>

                    <button class="close"
                            type="button"
                            data-dismiss="modal">
                        <span>&times;</span>
                    </button>

                </div>


                <div class="modal-body">

    <!-- Mot de passe actuel -->
    <div class="form-group">

        <label>Mot de passe actuel</label>

        <div class="input-group">

            <input type="password"
                   name="current_password"
                   id="current_password"
                   class="form-control"
                  autocomplete="current-password"
                   required>

            <div class="input-group-append">

                <span class="input-group-text"
                      onclick="togglePassword('current_password', this)">

                    <i class="fas fa-eye"></i>

                </span>

            </div>

        </div>

    </div>


    <!-- Nouveau mot de passe -->
    <div class="form-group">

        <label>Nouveau mot de passe</label>

        <div class="input-group">

            <input type="password"
                   name="new_password"
                   id="new_password"
                   class="form-control"
                   autocomplete="new-password"
                   required>

            <div class="input-group-append">

                <span class="input-group-text"
                      onclick="togglePassword('new_password', this)">

                    <i class="fas fa-eye"></i>

                </span>

            </div>

        </div>

    </div>


    <!-- Confirmer mot de passe -->
    <div class="form-group">

        <label>Confirmer mot de passe</label>

        <div class="input-group">

            <input type="password"
                   name="confirm_password"
                   id="confirm_password"
                   class="form-control"
                   autocomplete="new-password"
                   required>

            <div class="input-group-append">

                <span class="input-group-text"
                      onclick="togglePassword('confirm_password', this)">

                    <i class="fas fa-eye"></i>

                </span>

            </div>

        </div>

    </div>

</div>


                <div class="modal-footer">

                    <button class="btn btn-secondary"
                            type="button"
                            data-dismiss="modal">
                        Annuler
                    </button>

                    <button class="btn btn-warning"
                            type="submit">

                        Modifier

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- JS -->
<script src="vendor/jquery/jquery.min.js"></script>

<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<script src="js/sb-admin-2.min.js"></script>

<script>

function togglePassword(inputId, iconElement)
{
    var input = document.getElementById(inputId);
    var icon  = iconElement.querySelector("i");

    if(input.type === "password")
    {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    }
    else
    {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

</script>

<script>
setTimeout(function(){
    $(".alert").fadeOut("slow");
}, 6000);

if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.pathname);
}
</script>

</body>

</html>