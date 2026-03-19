<?php

session_start();
require_once("bd/database.php");

/* Vérifier connexion */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* ===============================
   Marquer comme lu
================================= */
$sql = "UPDATE messages
        SET statut = 'lu'
        WHERE receiver_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);

/* ===============================
   Récupérer TOUS les messages
================================= */
$sql = "SELECT m.*, u.nom, u.prenom
        FROM messages m
        JOIN utilisateur u ON m.sender_id = u.idutil
        WHERE m.sender_id = ? OR m.receiver_id = ?
        ORDER BY m.date_envoi ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id, $user_id]);

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">
    <title>Messagerie</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

<div id="wrapper">

    <?php include("menu.php"); ?>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <?php include("topbar.php"); ?>

            <!-- PAGE CONTENT -->
            <div class="container-fluid">

                <h1 class="h3 mb-4 text-gray-800">
                    Messagerie
                </h1>

                <!-- Bouton -->
                <div class="mb-3 text-right">

                    <button class="btn btn-primary btn-sm"
                            data-toggle="modal"
                            data-target="#composeModal">

                        <i class="fas fa-pen"></i>
                        Nouveau message

                    </button>

                </div>

                <!-- CHAT -->
                <div class="card shadow">

                    <div class="card-body"
                         id="chatBox"
                         style="height:500px; overflow-y:auto; background:#f8f9fc;">

                        <?php if($stmt->rowCount() > 0){ ?>
<?php echo $stmt->rowCount(); ?>
                            <?php while($msg = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>

                                <?php if($msg['sender_id'] == $user_id){ ?>

                                    <!-- Message envoyé -->
                                    <div class="d-flex justify-content-end mb-3">

                                        <div class="bg-primary text-white p-2 rounded"
                                             style="max-width:60%;">

                                            <?php echo $msg['message']; ?>

                                            <div class="small text-light text-right">
                                                <?php echo $msg['date_envoi']; ?>
                                            </div>

                                        </div>

                                    </div>

                                <?php } else { ?>

                                    <!-- Message reçu -->
                                    <div class="d-flex justify-content-start mb-3">

                                        <div class="bg-white p-2 rounded shadow-sm"
                                             style="max-width:60%;">

                                            <strong>
                                                <?php echo $msg['nom']." ".$msg['prenom']; ?>
                                            </strong>

                                            <div>
                                                <?php echo $msg['message']; ?>
                                            </div>

                                            <div class="small text-muted">
                                                <?php echo $msg['date_envoi']; ?>
                                            </div>

                                        </div>

                                    </div>

                                <?php } ?>

                            <?php } ?>

                        <?php } else { ?>

                            <div class="text-center text-muted">
                                Aucun message
                            </div>

                        <?php } ?>

                    </div>

                </div>

            </div>

        </div>

        <?php include("pieds.php"); ?>

    </div>

</div>

<!-- ===============================
     MODAL MESSAGE
================================= -->

<div class="modal fade" id="composeModal">

    <div class="modal-dialog">

        <div class="modal-content">

            <form method="POST" action="send_message.php">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Nouveau message
                    </h5>

                    <button class="close" data-dismiss="modal">
                        &times;
                    </button>

                </div>

                <div class="modal-body">

                    <div class="form-group">

                        <label>Destinataire</label>

                        <select name="receiver_id"
                                class="form-control"
                                required>

                            <?php
                            $users = $pdo->query("SELECT * FROM utilisateur WHERE idutil != $user_id");
                            while($u = $users->fetch()){
                            ?>

                                <option value="<?php echo $u['idutil']; ?>">
                                    <?php echo $u['nom']." ".$u['prenom']; ?>
                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="form-group">

                        <label>Message</label>

                        <textarea name="message"
                                  class="form-control"
                                  required></textarea>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="submit"
                            class="btn btn-success">

                        Envoyer

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- JS -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

<!-- Auto scroll bas -->
<script>
    var chatBox = document.getElementById("chatBox");
    chatBox.scrollTop = chatBox.scrollHeight;
</script>

</body>
</html>