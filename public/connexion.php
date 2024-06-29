<?php
include '../BDD/bdd.php';
session_start();

$message = '';
if(isset($_GET['message']) && $_GET['message'] == 'password_changed'){
    $message = 'Mot de passe changé avec succès. Veuillez vous reconnecter.';
}

$max_attempts = 5;
$block_time = 30;

$ip_address = $_SERVER['REMOTE_ADDR'];

$checkAttempts = $pdo->prepare('SELECT COUNT(*) AS attempt_count FROM login_attempts WHERE ip_address = ? AND attempt_time > NOW() - INTERVAL ? SECOND');
$checkAttempts->execute(array($ip_address, $block_time));
$attempts = $checkAttempts->fetch();

if($attempts['attempt_count'] >= $max_attempts){
    $message = 'Trop de tentatives échouées. Veuillez réessayer dans 30 secondes.';
    echo '<!DOCTYPE html>
          <html>
              <head>
                  <meta charset="utf-8" />
                  <title>Connexion</title>
              </head>
              <body>
                  <p align="center">'.htmlspecialchars($message).'</p>
              </body>
          </html>';
    exit();
} else {
    if(isset($_POST['envoie'])){
        if(!empty($_POST['pseudo']) AND !empty($_POST['mdp'])){
            $pseudo = htmlspecialchars($_POST['pseudo']);
            $mdp = $_POST['mdp'];

            try {
                $checkUser = $pdo->prepare('SELECT * FROM users WHERE pseudo = ?');
                $checkUser->execute(array($pseudo));
                $user = $checkUser->fetch();

                if($user && password_verify($mdp, $user['mdp'])){
                    $_SESSION['pseudo'] = $user['pseudo'];

                    $deleteAttempts = $pdo->prepare('DELETE FROM login_attempts WHERE ip_address = ?');
                    $deleteAttempts->execute(array($ip_address));
                    
                    if (isset($_GET['redirect'])) {
                        header('Location: ' . $_GET['redirect']);
                    } else {
                        header('Location: user.php');
                    }
                    exit();
                } else {
                    $insertAttempt = $pdo->prepare('INSERT INTO login_attempts (ip_address) VALUES (?)');
                    $insertAttempt->execute(array($ip_address));
                    
                    echo "Pseudo ou mot de passe incorrect";
                }
            } catch (Exception $e) {
                echo "Erreur lors de la connexion: " . $e->getMessage();
            }
        } else {
            echo "Veuillez compléter tous les champs...";
        }
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Connexion</title>
    </head>
    <body>
        <?php if ($message): ?>
            <p align="center"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form method="POST" action="" align="center">
            <input type="text" name="pseudo" autocomplete="off" placeholder="Pseudo">
            <br/>
            <input type="password" name="mdp" autocomplete="off" placeholder="Mot de passe">
            <br/><br/>
            <input type="submit" name="envoie" value="Se connecter">
        </form>
        <br/>
        <div align="center">
            <a href="inscription.php">Créer un compte</a>
        </div>
    </body>
</html>
