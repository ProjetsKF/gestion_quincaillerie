<?php
require_once '../bd/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sql = "INSERT INTO succursale
            (nomSuc, Quart, Comm, Aven)
            VALUES (:nomSuc, :Quart, :Comm, :Aven)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nomSuc' => $_POST['nomSuc'],
        ':Quart' => $_POST['Quart'],
        ':Comm' => $_POST['Comm'],
        ':Aven' => $_POST['Aven']
    ]);

    header("Location: index.php");
    exit;
}