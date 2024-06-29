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

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit();
}

if (isset($_POST['deconnexion'])) {
    session_destroy();
    header('Location: connexion.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Informations Utilisateur</title>
    </head>
    <body>
        <h2 align="center">Bienvenue, <?php echo htmlspecialchars($user['pseudo']); ?></h2>
        <p align="center">Email: <?php echo htmlspecialchars($user['mail']); ?></p>
        
        <form method="POST" action="" align="center">
            <input type="submit" name="deconnexion" value="Déconnexion">
        </form>
        <br/>
        <div align="center">
            <a href="changepwd.php">Changer le mot de passe</a>
        </div>
        <?php if ($user['role'] == 'admin'): ?>
            <br/>
            <div align="center">
                <a href="admin.php">Accéder à la page d'administration</a>
            </div>
        <?php endif; ?>
    </body>
</html>
