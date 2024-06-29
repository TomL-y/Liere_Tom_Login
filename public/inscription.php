<?php 
include '../BDD/bdd.php';
session_start();

function validatePassword($password) {
    if (strlen($password) < 8) {
        return "Le mot de passe doit contenir au moins 8 caractères, 1 majuscule et au moin 1 caractère spécial.";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return "Le mot de passe doit contenir au moins 8 caractères, 1 majuscule et au moin 1 caractère spécial.";
    }
    if (!preg_match('/[\W]/', $password)) {
        return "Le mot de passe doit contenir au moins 8 caractères, 1 majuscule et au moin 1 caractère spécial.";
    }
    return true;
}

$error_message = '';

if(isset($_POST['envoie'])){
    if(!empty($_POST['pseudo']) AND !empty($_POST['mdp']) AND !empty($_POST['mail'])){
        $pseudo = htmlspecialchars($_POST['pseudo']);
        $mail = htmlspecialchars($_POST['mail']);

        if (!filter_var($mail, FILTER_VALIDATE_EMAIL) || !preg_match('/@.*\./', $mail)) {
            $error_message = "Adresse email non valide.";
        } else {
            $mdp = $_POST['mdp'];
            $validationResult = validatePassword($mdp);
            if ($validationResult !== true) {
                $error_message = $validationResult;
            } else {
                $checkMail = $pdo->prepare('SELECT * FROM users WHERE mail = ?');
                $checkMail->execute(array($mail));
                if($checkMail->rowCount() > 0){
                    $error_message = "Cette adresse email est déjà utilisée.";
                } else {
                    $mdp = password_hash($mdp, PASSWORD_DEFAULT);

                    try {
                        $insertUser = $pdo->prepare('INSERT INTO users(pseudo, mail, mdp) VALUES(?, ?, ?)');
                        $insertUser->execute(array($pseudo, $mail, $mdp));

                        $_SESSION['pseudo'] = $pseudo;
                        header('Location: user.php');
                        exit();
                    } catch (Exception $e) {
                        $error_message = "Erreur lors de la création du compte: " . $e->getMessage();
                    }
                }
            }
        }
    } else {
        $error_message = "Veuillez compléter tous les champs...";
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Inscription</title>
    </head>
    <body>
        <?php if ($error_message): ?>
            <p align="center" style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <form method="POST" action="" align="center">
            <input type="text" name="pseudo" autocomplete="off" placeholder="Pseudo" value="<?php echo isset($_POST['pseudo']) ? htmlspecialchars($_POST['pseudo']) : ''; ?>">
            <br/>
            <input type="text" name="mail" autocomplete="off" placeholder="Email" value="<?php echo isset($_POST['mail']) ? htmlspecialchars($_POST['mail']) : ''; ?>">
            <br/>
            <input type="password" name="mdp" autocomplete="off" placeholder="Mot de passe">
            <br/><br/>
            <input type="submit" name="envoie" value="S'inscrire">
        </form>
        <br/>
        <div align="center">
            <a href="connexion.php">Avez-vous déjà un compte ?</a>
        </div>
    </body>
</html>
