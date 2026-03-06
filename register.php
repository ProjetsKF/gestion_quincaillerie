<?php
require_once 'bd/database.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* Récupération des données */

    $nom     = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $postnom = isset($_POST['postnom']) ? trim($_POST['postnom']) : '';
    $prenom  = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
    $email   = isset($_POST['email']) ? trim($_POST['email']) : '';
    $motpass = isset($_POST['motpass']) ? $_POST['motpass'] : '';
    $conf    = isset($_POST['conf']) ? $_POST['conf'] : '';
    $idsuc   = isset($_POST['idsuc']) ? $_POST['idsuc'] : '';

    /* Vérification */

    if ($nom && $postnom && $prenom && $email && $motpass && $conf && $idsuc) {

        $checkSql = "SELECT idutil FROM utilisateur WHERE email = :email LIMIT 1";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute(array(
            ':email' => $email
        ));

        if ($checkStmt->fetch()) {

            $message = "Cet email est déjà utilisé.";
            $message_type = "error";

        } 
        elseif ($motpass != $conf) {

            $message = "Les mots de passe ne correspondent pas.";
            $message_type = "error";

        } 
        else {

            $password_hash = password_hash($motpass, PASSWORD_DEFAULT);

            $sql = "INSERT INTO utilisateur
                    (email, motPass, rol, idSuc, nom, postnom, prenom)
                    VALUES
                    (:email, :motpass, :rol, :idsuc, :nom, :postnom, :prenom)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute(array(
                ':email'   => $email,
                ':motpass' => $password_hash,
                ':rol'     => 0,
                ':idsuc'   => $idsuc,
                ':nom'     => $nom,
                ':postnom' => $postnom,
                ':prenom'  => $prenom
            ));

            $message = "Utilisateur créé avec succès.";
            $message_type = "success";
        }

    } 
    else {

        $message = "Veuillez remplir tous les champs.";
        $message_type = "error";

    }
}

/* Charger les succursales */

$sql = "SELECT idsuc, nomSuc FROM succursale ORDER BY nomSuc";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$suc = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>BISIKOMASH - Inscription</title>

    <!-- Fonts -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- SB Admin CSS -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #4e73df, #224abe);
        }

        /* Carte arrondie */
        .card {
            border-radius: 15px;
            overflow: hidden;
        }

        /* Image à gauche */
        .register-image {
            background: url("img/login.png") center center;
            background-size: cover;
            background-repeat: no-repeat;
            min-height: 100%;

            border-top-left-radius: 15px;
            border-bottom-left-radius: 15px;
        }
    </style>

</head>

<body>

    <div class="container">

        <div class="card shadow-lg my-5 border-0">
            <div class="card-body p-0">

                <div class="row">

                    <!-- IMAGE GAUCHE -->
                    <div class="col-lg-5 d-none d-lg-block register-image"></div>

                    <!-- FORMULAIRE DROITE -->
                    <div class="col-lg-7">
                        <div class="p-5">

                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">
                                    Créer un compte
                                </h1>
                            </div>

                            <form method="post">
                                <?php if (!empty($message)) : ?>

                                        <div class="alert <?php echo $message_type === 'error' ? 'alert-danger' : 'alert-success'; ?> alert-dismissible fade show" role="alert">

                                            <?php echo htmlspecialchars($message); ?>

                                            <button type="button" class="close" data-dismiss="alert">
                                                <span>&times;</span>
                                            </button>

                                        </div>

                                <?php endif; ?>

                                <div class="form-group row">
                                    <div class="col-sm-4 mb-3 mb-sm-0">
                                        <input type="text"
                                            class="form-control form-control-user" name="nom" 
                                            placeholder="Nom">
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text"
                                            class="form-control form-control-user" name="postnom" 
                                            placeholder="Postnom">
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text"
                                            class="form-control form-control-user" name="prenom" 
                                            placeholder="Prénom">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <input type="email"
                                        class="form-control form-control-user" name="email" 
                                        placeholder="Adresse e-mail">
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0" style="position:relative;">

                                        <input type="password"
                                               class="form-control form-control-user"
                                               name="motpass"
                                               id="motpass"
                                               placeholder="Mot de passe">

                                        <i class="fas fa-eye"
                                           onclick="togglePassword('motpass', this)"
                                           style="position:absolute; right:15px; top:50%; transform:translateY(-50%); cursor:pointer; color:#6c757d;">
                                        </i>

                                        </div>


                                        <div class="col-sm-6 mb-3 mb-sm-0" style="position:relative;">

                                        <input type="password"
                                               class="form-control form-control-user"
                                               name="conf"
                                               id="conf"
                                               placeholder="Confirmer mot de passe">

                                        <i class="fas fa-eye"
                                           onclick="togglePassword('conf', this)"
                                           style="position:absolute; right:15px; top:50%; transform:translateY(-50%); cursor:pointer; color:#6c757d;">
                                        </i>

                                        </div>
                                    <div class="col-sm-12 mt-3">
                                        
                                        <select class="form-control form-control-user" name="idsuc">

                                                <?php foreach ($suc as $su): ?>

                                                <option value="<?= $su['idsuc'] ?>">
                                                    <?= htmlspecialchars($su['nomSuc']) ?>
                                                </option>

                                                <?php endforeach; ?>

                                        </select>
                                        
                                        
                                    </div>
                                </div>
                                <button type="submit" 
                                    class="btn btn-primary btn-user btn-block">
                                    Créer nouveau compte
                                </button>

                                <hr>

                                <a href="#"
                                    class="btn btn-google btn-user btn-block">
                                    <i class="fab fa-google fa-fw"></i>
                                    S'inscrire avec Google
                                </a>

                                <a href="#"
                                    class="btn btn-facebook btn-user btn-block">
                                    <i class="fab fa-facebook-f fa-fw"></i>
                                    S'inscrire avec Facebook
                                </a>

                            </form>

                            <hr>

                            <div class="text-center">
                                <a class="small" href="#">
                                    Mot de passe oublié ?
                                </a>
                            </div>

                            <div class="text-center">
                                <a class="small" href="index.php">
                                    Déjà un compte ? Connexion !
                                </a>
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

function togglePassword(fieldId, icon) {

    const input = document.getElementById(fieldId);

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } 
    else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }

}

</script>

</body>

</html>