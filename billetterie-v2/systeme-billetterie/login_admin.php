<?php

session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // Dans ce cas là, la personne est toujours connectée, on la redirige.
    header('Location: dashboard_admin.php');
    exit(); // Coupe PHP
}

$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

$erreur = null;
if ($methode == "POST") {
    $login = filter_input(INPUT_POST, "login");
    $_SESSION["login"] = $login;
    $password = filter_input(INPUT_POST, "password");

    $pdo = new PDO("mysql:host=localhost:3306;dbname=billetterie_hetic", "root", "");
    $requete = $pdo->prepare("
        SELECT * FROM admins
        WHERE login = :login
    ");
    $requete->execute([
        ":login" => $login
    ]);
    $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);
    if ($utilisateur !== false && password_verify($password, $utilisateur['password'])) {
        $_SESSION["loggedin"] = true;
        header('Location: dashboard_admin.php');
        exit();
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

    <link rel="stylesheet" href="style/login_admin.css">
    <title>Document</title>
</head>

<body>
    <header>
        <h1>Dauphinois</h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="#">Billets</a></li>
                <li><a href="../systeme-authentification/users/login.php">Admin</a></li>
                <li><a href="dashboard_admin.php">Dashboard</a></li>
            </ul>
        </nav>
    </header>

    <body>
        <div class="form_login ">
            <h1>Connectez-vous !</h1>
            <?php if ($erreur !== null) : ?>
                <p style="background: #FAA; color: red; padding: .5rem .75rem">
                    <?= $erreur ?>
                </p>
            <?php endif; ?>
            <form method="POST">
                <label for="login">Identifiant</label>
                <input type="text" id="login" name="login">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password">

                <input type="submit" value="Connexion">
            </form>
        </div>
        <footer>
            <p>&copy; 2023 Dauphinois. Tous droits réservés.</p>
        </footer>

        <!-- <div class="print_events">
    <h2>Liste des évènements :</h2>
    <?= print_events($evenements, $order) ?> -->
        </div>
    </body>

</html>