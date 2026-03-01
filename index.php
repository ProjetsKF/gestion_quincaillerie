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

                                    <form>

                                        <div class="form-group">
                                            <input type="email"
                                                class="form-control form-control-user"
                                                placeholder="Saisissez votre adresse e-mail...">
                                        </div>

                                        <div class="form-group">
                                            <input type="password"
                                                class="form-control form-control-user"
                                                placeholder="Mot de passe">
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

                                        <a href="dashboard.php"
                                            class="btn btn-primary btn-user btn-block">
                                            Login
                                        </a>

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

</body>

</html>