<?php
ob_start();
//header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers,Access-Control-Allow-Headers,Authorization, X-Requested-With");
$erreur = null;
$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

if ($methode == 'POST') {

    include_once "../config/Database.php";
    include_once "../models/Users.php";

    $database = new Database();
    $db = $database->getConnection();

    $user = new Users($db);


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

            $user->login = $login;
            $user->password = password_hash($password, PASSWORD_DEFAULT);
            $user->phone_number = $phone_number;
            $user->first_name = $first_name;
            $user->last_name = $last_name;
            $user->email = $email;

            if ($user->add_user()) {
                header("Location: login.php");
                exit();
                http_response_code(201);
                //                json_encode(["message"=>"Utilisateur ajouté"]);
            }
            //else{
            //                http_response_code(503);
            //                echo json_encode(["message"=>"Utilisateur ajouté"]);
            //
        }
    }
}
//else{
//    http_response_code(400);
//    echo json_encode(["message"=>"Mauvaise requete du json"]);
//}


function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
function isValidPhone_number($phone_number)
{
    $phone = "#(\+[0-9]{2}\([0-9]\))?[0-9]{10}#";
    return preg_match($phone, $phone_number);
}

?>

<!DOCTYPE html>
<html lang="en-fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
</head>

<body>
    <h1>Inscription</h1>
    <?php if ($erreur !== null) : ?>
        <p style="background: #FAA; color: red; padding: .5rem .75rem">
            <?= $erreur ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <label for="login">Identifiant</label>
        <input type="text" id="login" name="login" required>

        <label for="first_name">Prénom</label>
        <input type="text" id="first_name" name="first_name" required>

        <label for="last_name">Nom</label>
        <input type="text" id="last_name" name="last_name" required>

        <label for="email">Email</label>
        <input type="text" id="email" name="email" required>

        <label for="phone_number">Numero de telephone</label>
        <input type="text" id="phone_number" name="phone_number" required>

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>

        <input type="submit" name="inscrire" value="Inscrire">
    </form>
    <div>
        <a href="login.php">Retour</a>
    </div>
    <?php include '../../tpl/footer.php'; ?>