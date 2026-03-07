<?php
session_start();
require_once 'bd/database.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email   = isset($_POST['email']) ? trim($_POST['email']) : '';
    $motpass = isset($_POST['motpass']) ? $_POST['motpass'] : '';

    if (!empty($email) && !empty($motpass)) {

        $sql = "SELECT * FROM utilisateur WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':email' => $email
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {

            /* Vérification du mot de passe */

            if (!password_verify($motpass, $user['motPass'])) {

                $message = "Mot de passe incorrect.";
                $message_type = "error";

            }

            /* Vérifier si compte suspendu */

            elseif ($user['statut'] === 'Suspendu') {

                $message = "Votre compte est suspendu. Contactez l'administrateur.";
                $message_type = "error";

            }

            /* Vérifier si compte en attente */

            elseif ($user['statut'] === 'En attente') {

                $message = "Votre compte est en attente de validation.";
                $message_type = "error";

            }

            /* Compte actif */

            elseif ($user['statut'] === 'Actif') {

                $_SESSION['user_id'] = $user['idutil'];
                $_SESSION['nom'] = $user['nom'];
                $_SESSION['prenom'] = $user['prenom'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['rol'];
                $_SESSION['idsuc'] = $user['idSuc'];

                header("Location: dashboard.php");
                exit;

            }

            else {

                $message = "Statut du compte inconnu.";
                $message_type = "error";

            }

        } else {

            $message = "Adresse email introuvable.";
            $message_type = "error";

        }

    } else {

        $message = "Veuillez remplir tous les champs.";
        $message_type = "error";

    }

}
?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>BISIKOMASH - Connexion</title>

    <!-- Fonts -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- SB Admin CSS -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #4e73df, #224abe);
        }

        .login-image {
            background: url("img/login.png") center center;
            background-size: cover;
            background-repeat: no-repeat;
            min-height: 100%;

                /* Arrondir uniquement les coins gauches */
    border-top-left-radius: 15px;
    border-bottom-left-radius: 15px;
        }

        .card {
            border-radius: 15px;
        }
    </style>

</head>

<body>

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card shadow-lg my-5 border-0">
                    <div class="card-body p-0">

                        <div class="row">

                            <!-- IMAGE À GAUCHE -->
                            <div class="col-lg-6 d-none d-lg-block login-image"></div>

                            <!-- FORMULAIRE À DROITE -->
                            <div class="col-lg-6">
                                <div class="p-5">

                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">
                                            Content de te revoir !
                                        </h1>
                                    </div>
                                    <?php if (!empty($message)) : ?>

                                    <div class="alert <?php echo $message_type === 'error' ? 'alert-danger' : 'alert-success'; ?>">

                                    <?php echo htmlspecialchars($message); ?>

                                    </div>

                                    <?php endif; ?>
                                <form method="POST">

                                        <div class="form-group">
                                            <input type="email" name="email"
                                                class="form-control form-control-user"
                                                placeholder="Saisissez votre adresse e-mail...">
                                        </div>

                                       <div class="form-group" style="position:relative;">

                                                    <input type="password"
                                                           name="motpass"
                                                           id="motpass"
                                                           class="form-control form-control-user"
                                                           placeholder="Mot de passe">

                                                    <i class="fas fa-eye"
                                                       onclick="togglePassword()"
                                                       style="position:absolute;
                                                              right:15px;
                                                              top:50%;
                                                              transform:translateY(-50%);
                                                              cursor:pointer;
                                                              color:#6c757d;">
                                                    </i>

                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox"
                                                    class="custom-control-input"
                                                    id="remember">
                                                <label class="custom-control-label"
                                                    for="remember">
                                                    Souviens-toi
                                                </label>
                                            </div>
                                        </div>

                                        <button type="submit"
                                        class="btn btn-primary btn-user btn-block">
                                        Se connecter
                                        </button>

                                        <hr>

                                        <a href="#"
                                            class="btn btn-google btn-user btn-block">
                                            <i class="fab fa-google fa-fw"></i>
                                            Se connecter avec Google
                                        </a>

                                        <a href="#"
                                            class="btn btn-facebook btn-user btn-block">
                                            <i class="fab fa-facebook-f fa-fw"></i>
                                            Se connecter avec Facebook
                                        </a>

                                    </form>

                                    <hr>

                                    <div class="text-center">
                                        <a class="small" href="#">
                                            Mot de passe oublié ?
                                        </a>
                                    </div>

                                    <div class="text-center">
                                        <a class="small" href="register.php">
                                            Créez un compte !
                                        </a>
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script>

function togglePassword() {

    var input = document.getElementById("motpass");

    if (input.type === "password") {
        input.type = "text";
    } else {
        input.type = "password";
    }

}

</script>

</body>

</html>