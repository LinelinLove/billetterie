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
        header('Location: ../../systeme-billetterie/dashboard_user.php');
        exit();
    } else {
        $erreur = "Identifiants incorrects !";
    }
}

?>

<?php
$titre = "Connexion";
include '../../tpl/auth_header.php';
?>

<div class="show_ticket_container">

    <h1>Connexion</h1>

    <?php if ($erreur !== null) : ?>
        <p style="background: #FAA; color: red; padding: .5rem .75rem">
            <?= $erreur ?>
        </p>
    <?php endif; ?>

    <form method="POST" class="show_ticket_form">
        <div class="show_ticket_div">
            <div class="show_ticket_form_input">
                <label for="login">Identifiant :</label>
                <label for="password">Mot de passe :</label>
            </div>

            <div class="show_ticket_form_input">
                <input type="text" id="login" name="login" required>
                <input type="password" id="password" name="password" required>
            </div>
        </div>
        <div class="show_ticket_button">
            <input type="submit" value="Connexion" class="show_ticket_btn">
        </div>
    </form>

</div>

<?php include '../../tpl/footer.php'; ?>