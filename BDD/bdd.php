<?php
$jsonFilePath = 'C:/wamp64/mdp/mdp.json';

$jsonData = file_get_contents($jsonFilePath);

$credentials = json_decode($jsonData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die('Erreur de lecture du fichier JSON : ' . json_last_error_msg());
}
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=espace_membres;charset=utf8',
        $credentials['pseudo'],
        $credentials['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>