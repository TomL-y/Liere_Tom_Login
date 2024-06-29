<?php
session_start();
if (!isset($_SESSION['pseudo'])) {
    header('Location: connexion.php?redirect=changepwd.php');
    exit();
}

include '../BDD/bdd.php';

function validatePassword($password) {
    if (strlen($password) < 8) {
        return "Le mot de passe doit contenir au moins 8 caractères.";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return "Le mot de passe doit contenir au moins une majuscule.";
    }
    if (!preg_match('/[\W]/', $password)) {
        return "Le mot de passe doit contenir au moins un caractère spécial.";
    }
    return true;
}

if(isset($_POST['changer'])){
    if(!empty($_POST['ancien_mdp']) AND !empty($_POST['nouveau_mdp'])){
        $pseudo = $_SESSION['pseudo'];
        $ancien_mdp = $_POST['ancien_mdp'];
        $nouveau_mdp = $_POST['nouveau_mdp'];

        $checkUser = $pdo->prepare('SELECT * FROM users WHERE pseudo = ?');
        $checkUser->execute(array($pseudo));
        $user = $checkUser->fetch();

        if($user && password_verify($ancien_mdp, $user['mdp'])){
            if(password_verify($nouveau_mdp, $user['mdp'])){
                echo "Le nouveau mot de passe ne peut pas être le même que l'ancien.";
                exit();
            }

            $validationResult = validatePassword($nouveau_mdp);
            if ($validationResult !== true) {
                echo $validationResult;
                exit();
            }

            $nouveau_mdp_hashed = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
            $updateMdp = $pdo->prepare('UPDATE users SET mdp = ? WHERE pseudo = ?');
            $updateMdp->execute(array($nouveau_mdp_hashed, $pseudo));
            session_destroy();
            header('Location: connexion.php?message=password_changed');
            exit();
        } else {
            echo "Ancien mot de passe incorrect.";
        }
    } else {
        echo "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Changer Mot de Passe</title>
    </head>
    <body>
        <form method="POST" action="" align="center">
            <input type="password" name="ancien_mdp" autocomplete="off" placeholder="Ancien mot de passe">
            <br/>
            <input type="password" name="nouveau_mdp" autocomplete="off" placeholder="Nouveau mot de passe">
            <br/><br/>
            <input type="submit" name="changer" value="Changer le mot de passe">
        </form>
    </body>
</html>
