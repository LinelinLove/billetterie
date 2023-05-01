<?php

header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers,Access-Control-Allow-Headers,Authorization, X-Requested-With");

session_start();

$erreur = null;
$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");


include_once "../config/Database.php";
include_once "../models/Users.php";

$database = new Database();
$db = $database->getConnection();

$user = new Users($db);


if ($methode == "POST") {
    $login = filter_input(INPUT_POST, "login");
    $_SESSION["login"] = $login;
    $password = filter_input(INPUT_POST, "password");
    $user->login = $login;
    $stmt = $user->login();
    $stmt = $stmt->fetch(PDO::FETCH_ASSOC);

    if (password_verify($password, $stmt["password"])) {

        $_SESSION["loggedin"] = true;
        $_SESSION["token"] = bin2hex(random_bytes(32));
        $sql = "INSERT INTO tokens (token) VALUE(:token)";

        $query = $db->prepare($sql);
        $query->execute([
            ":token" => $_SESSION["token"]
        ]);
        header('Location: ../../systeme-billetterie/dashboard_admin.php');
        exit(); // Coupe PHP
        //        echo "Vous etes log";
    } else {
        $erreur = "Identifiants incorrects !";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="login_admin.css">
</head>

<body>

    <header>
        <h1>Dauphinois</h1>
        <nav>
            <ul>
                <li><a href="../../systeme-billetterie/index.php">Accueil</a></li>
                <li><a href="show_ticket.php">Billets</a></li>
                <li><a href="login.php">Admin</a></li>
            </ul>
        </nav>
    </header>
    <h1>Connectez-vous !</h1>
    <?php if ($erreur !== null) : ?>
        <p style="background: #FAA; color: red; padding: .5rem .75rem">
            <?= $erreur ?>
        </p>
    <?php endif; ?>
    <form method="POST">
        <label for="login">Identifiant</label>
        <input type="text" id="login" name="login" required>
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>

        <input type="submit" value="Connexion">
    </form>
    <?php if (isset($_SESSION["loggedin"])) {
        echo ("
            <button><a href='logout.php'>Deconnexion</a></button>
            <button><a href='../../systeme-billetterie/dashboard_admin.php'>Dashboard</a></button>
        ");
    } ?>
    <footer>
        <p>&copy; 2023 Dauphinois. Tous droits réservés.</p>
    </footer>
</body>