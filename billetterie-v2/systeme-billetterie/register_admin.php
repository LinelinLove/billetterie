<?php
session_start();

// if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
//     // Dans ce cas là, la personne n'est pas connectée, on la redirige.
//     header('Location: login.php');
//     exit(); // Coupe PHP
// }

$pdo = new PDO("mysql:host=localhost:3306;dbname=billetterie_hetic", "root", "");
$erreur = null;
$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");
$name_event = filter_input(INPUT_POST, "name_event");


function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function isValidPhone_number($phone_number)
{
    $phone = "#(\+[0-9]{2}\([0-9]\))?[0-9]{10}#";
    return preg_match($phone, $phone_number);
}

if ($methode == "POST") {

    if (isset($_POST["inscrire"])) {
        $email = filter_input(INPUT_POST, "email");
        $login = filter_input(INPUT_POST, "login");
        $password = filter_input(INPUT_POST, "password");
        $phone_number = filter_input(INPUT_POST, "phone_number");
        $first_name = filter_input(INPUT_POST, "first_name");
        $last_name = filter_input(INPUT_POST, "last_name");

        // Validation de l'email
        $isValidEmail = isValidEmail($email);
        if (!$isValidEmail) {
            $erreur = "Votre email n'est pas valide et/ou il est déjà utilisé.";
        }

        // Validation du numero de telephone
        $isValidPhone_number = isValidPhone_number($phone_number);
        if (!$isValidPhone_number) {
            $erreur = "Ce numéro de téléphone n'est pas valide et/ou déjà utilisé.";
        }

        $isPasswordValid = true;
        if (!preg_match('/[A-Z]/', $password)) {
            $erreur = "Le mot de passe doit contenir au moins une majuscule.";
            $isPasswordValid = false;
        }
        if (!preg_match('/[a-z]/', $password)) {
            $erreur = "Le mot de passe doit contenir au moins une minuscule.";
            $isPasswordValid = false;
        }
        if (!preg_match('/[0-9]/', $password)) {
            $erreur = "Le mot de passe doit contenir au moins un chiffre.";
            $isPasswordValid = false;
        }
        if (!preg_match('/[^\w]/', $password)) {
            $erreur = "Le mot de passe doit contenir au moins un caractère spécial.";
            $isPasswordValid = false;
        }
        if (strlen($password) < 8 || strlen($password) > 20) {
            $erreur = "Le mot de passe doit contenir entre 8 et 20 caractères.";
            $isPasswordValid = false;
        }

        if ($erreur === null) {


            $requete_mail = $pdo->prepare("
        SELECT email FROM admins WHERE email = '$email';
        ");
            $requete_mail->execute();

            $result_email = $requete_mail->fetchAll();

            $requete_login = $pdo->prepare("
            SELECT login FROM admins WHERE login = '$login';
            ");
            $requete_login->execute();

            $result_login = $requete_mail->fetchAll();

            // si aucun mail ne correspond dans la BDD, tableau vide
            // sinon il récupère une valeur
            if (count($result_email) > 0) {
                $erreur = "Mail déjà utilisé.";
            } else if (count($result_login) > 0) {
                $erreur = "Login déjà utilisé.";
            } else {
                $requete = $pdo->prepare("
                    INSERT INTO admins (email, login ,password, phone_number, first_name, last_name) 
                    SELECT :email, :login ,:password, :phone_number, :first_name, :last_name
                    WHERE NOT EXISTS (
                    SELECT 1 FROM admins WHERE email = :email OR phone_number = :phone_number
            );
                    ");
                $requete->execute([
                    ":email" => $email,
                    ":login" => $login,
                    ":password" => password_hash($password, PASSWORD_DEFAULT),
                    ":phone_number" => $phone_number,
                    ":first_name" => $first_name,
                    ":last_name" => $last_name
                ]);

                header("Location: login_admin.php");
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en-fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="style/register_admin.css">
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

    <form method="POST" class="login-form">
        <h1>Inscription</h1>
        <?php if ($erreur !== null) : ?>
            <p style="background: #FAA; color: red; padding: .5rem .75rem; margin: 0 0 10px 0">
                <?= $erreur ?>
            </p>
        <?php endif; ?>

        <label for="login">Identifiant</label>
        <input type="text" id="login" name="login" placeholder="identifiant">

        <label for="first_name">Prénom</label>
        <input type="text" id="first_name" name="first_name" placeholder="prénom">

        <label for="last_name">Nom</label>
        <input type="text" id="last_name" name="last_name" placeholder="nom">

        <label for="email">Email</label>
        <input type="text" id="email" name="email" placeholder="email">

        <label for="phone_number">Numero de téléphone</label>
        <input type="text" id="phone_number" name="phone_number" placeholder="téléphone">

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" placeholder="mot de passe"><br>

        <input type="submit" name="inscrire" value="Inscrire" class="connexion">

    </form>
    <div>
        <a href="dashboard_admin.php">Retour</a>
    </div>

    <footer>
        <p>&copy; 2023 Dauphinois. Tous droits réservés.</p>
    </footer>
</body>

</html>