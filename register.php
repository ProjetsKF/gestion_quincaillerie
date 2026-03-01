<?php
require_once 'bd/database.php';

$message = '';
$message_type = '';

/* Vérifier si le formulaire est soumis */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Récupération et nettoyage des données
    $nom = trim($_POST['nom'] );
    $postnom  = trim($_POST['postnom'] );
    $prenom  = trim($_POST['prenom'] );
    $email      = trim($_POST['email'] );
    $motpass   = $_POST['motpass'] ;
    $conf   = $_POST['conf'] ;
    $idsuc   = $_POST['idsuc'] ;
    //$role_id    = isset($_POST['role_id']) ? (int) $_POST['role_id'] : 0;

    // 2. Vérification des champs obligatoires
    if ($nom && $postnom &&$prenom &&  $email && $motpass && $conf) {

        // 3. Vérifier si l'email existe déjà
        $checkSql = "SELECT idutil FROM Utilisateur WHERE email = :email LIMIT 1";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([
            ':email' => $email
        ]);

        if ($checkStmt->fetch()) {

            $message = "Cet email est déjà utilisé.";
            $message_type = 'Erreur';

        } 
        elseif ($motpass!=$conf) {
            $message = "Les deux mots de passe ne sont pas identiques.";
            $message_type = 'Erreur';
        }

        else {

            // 4. Hash du mot de passe
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // 5. Insertion dans users
            $sql = "INSERT INTO Utilisateur
                    (nom, postnom, prenom, email, motpass, idSuc)
                    VALUES
                    (:nom, :postnom, :prenom, :email, :motpass,(SELECT idSuc FROM Succursale WHERE nomSuc=:idSuc) )";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom'    => $nom,
                ':postnom' => $postnom,
                ':prenom'  => $prenom,
                ':email'      => $email,
                ':motpass'   => $password_hash,
                ':idSuc'     => $idsuc
            ]);

            // 6. Récupérer l’ID de l’utilisateur créé
            /*$user_id = $pdo->lastInsertId();
            }*/

            $message = "Utilisateur créé avec succès.";
            $message_type = 'success';
        }

    }
     else {
        $message = "Veuillez remplir tous les champs.";
        $message_type = 'error';
    }
}

/* ===============================
   POUR CHARGEMENT LISTE DEROULANTE SUCCURSALE
================================= */

$req = "SELECT * FROM Succursale ";

$res = $pdo->prepare($req);
$res->execute();

$suc = $res->fetchAll();

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
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password"
                                            class="form-control form-control-user" name="motpass" 
                                            placeholder="Mot de passe">
                                    </div>
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" name="conf" 
                                            class="form-control form-control-user"
                                            placeholder="Confirmer mot de passe">
                                    </div>
                                    <div class="col-sm-12 mt-3">
                                        
                                        <select class="form-control form-control-user" name="idsuc">
                                            <?php foreach ($suc as $su) : ?>
                                            <option><?= htmlspecialchars($su['nomSuc']) ?></option>  
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

</body>

</html>