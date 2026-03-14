<?php

/* démarrer la session seulement si nécessaire */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Vérifier si utilisateur connecté */

if (!isset($_SESSION['user_id'])) {

    header("Location: /gestion_quincaillerie/index.php");
    exit();

}


/* Vérifier si c'est un administrateur */

if ($_SESSION['role'] != 1) {

echo '
<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

<script>

Swal.fire({
    icon: "error",
    title: "Accès refusé",
    text: "Cette page est réservée à l\'administrateur.",
    confirmButtonText: "Retour"
}).then(() => {

    window.location = "/gestion_quincaillerie/dashboard.php";

});

</script>

</body>
</html>
';

exit();

}

?>