<?php

require_once("auth_admin.php");
require_once("bd/database.php");

if(isset($_POST['ids']))
{

    $ids = $_POST['ids'];

    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $sql = "DELETE FROM activity_log
            WHERE id IN ($placeholders)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute($ids);

}

header("Location: activity_log.php");
exit();