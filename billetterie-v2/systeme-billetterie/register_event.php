<?php
session_start();

// Première chose, si la personne n'est pas connectée, c'est dangereux de lui afficher la page sécurisée. On la renvoie vers le login.
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Dans ce cas là, la personne n'est pas connectée, on la redirige.
    header('Location: ../systeme-authentification/users/login.php');
    exit(); // Coupe PHP
}

$login = $_SESSION["login"];


$pdo = new PDO("mysql:host=localhost:3306;dbname=billetterie_hetic", "root", "");

if (isset($_POST['Inscription'])) {
    $inscription = $_POST['Inscription'];
    };
    $requete = $pdo->prepare("
    INSERT INTO users (login, password) VALUES(:login, :password)
");
$requete->execute([
    ":login" => $login,
    ":password" => password_hash($password, PASSWORD_DEFAULT)
]);



?>