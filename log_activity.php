<?php

function logActivity($pdo, $user_id, $action, $description)
{
    $sql = "INSERT INTO activity_log (user_id, action, description)
            VALUES (?, ?, ?)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $user_id,
        $action,
        $description
    ]);
}