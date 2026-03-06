<?php

session_start();

/* Vérifier si utilisateur connecté */

if(!isset($_SESSION['user_id'])){
header("Location: ../index.php");
exit;
}

/* Vérifier si c'est un administrateur */

if($_SESSION['role'] != 1){

echo '

<!DOCTYPE html>
<html>
<head>

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

window.location = "../dashboard.php";

});

</script>

</body>
</html>

';

exit;

}

?>