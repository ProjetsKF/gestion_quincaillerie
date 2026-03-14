<?php

session_start();

require_once("../bd/database.php");

if($_SERVER['REQUEST_METHOD'] === 'POST')
{

    $current_password = $_POST['current_password'];
    $new_password     = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $id = $_SESSION['user_id'];

    $sql = "SELECT motPass
            FROM utilisateur
            WHERE idutil = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);


    /* vérifier ancien mot de passe */

    if(!password_verify($current_password, $user['motPass']))
    {
        header("Location: ../profile.php?error=wrongpassword");
        exit();
    }


    /* vérifier confirmation */

    if($new_password !== $confirm_password)
    {
        header("Location: ../profile.php?error=nomatch");
        exit();
    }


    /* hash nouveau mot de passe */

    $newHash = password_hash($new_password, PASSWORD_DEFAULT);


    $sql = "UPDATE utilisateur
            SET motPass = ?
            WHERE idutil = ?";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $newHash,
        $id
    ]);


    /* succès */

    header("Location: ../profile.php?success=passwordchanged");
    exit();

}