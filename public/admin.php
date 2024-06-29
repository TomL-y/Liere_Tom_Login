<?php
session_start();

if (!isset($_SESSION['pseudo'])) {
    header('Location: connexion.php');
    exit();
}

include '../BDD/bdd.php';

$pseudo = $_SESSION['pseudo'];
$getUser = $pdo->prepare('SELECT * FROM users WHERE pseudo = ?');
$getUser->execute(array($pseudo));
$user = $getUser->fetch();

if (!$user || $user['role'] != 'admin') {
    echo "Accès refusé. Vous n'avez pas les autorisations nécessaires pour accéder à cette page.";
    exit();
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Page d'Administration</title>
    </head>
    <body>
        <h2 align="center">Page d'administration !</h2>
    </body>
</html>
