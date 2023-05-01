<?php
session_start();
// setcookie('localhost', '', time() - 3600, '/');

require "ticket_generation.php";
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Dans ce cas là, la personne n'est pas connectée, on la redirige.
    header('Location: ../systeme-authentification/users/login.php');
    exit(); // Coupe PHP
}

$pdo = new PDO("mysql:host=localhost:3306;dbname=billetterie_hetic", "root", "");
$erreur = null;
$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");
$name_event = filter_input(INPUT_POST, "name_event");
$event_id = filter_input(INPUT_POST, "id");


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

        if ($erreur === null) {


            $requete_mail = $pdo->prepare("
        SELECT email, id FROM users WHERE email = '$email';
        ");
            $requete_mail->execute();

            $results = $requete_mail->fetchAll();

            // si aucun mail ne correspond dans la BDD, tableau vide
            // sinon il récupère une valeur
            if (count($results) > 0) {

                // si le couple event_id et user_id existe, le client est déja inscrit a cet evenement
                $id = $results[0]["id"];

                $requete_is_inscrit = $pdo->prepare("
                    SELECT * FROM `events_users` where event_id = '$event_id' and user_id = '$id'
                    
                    ");

                $requete_is_inscrit->execute();
                $resultat_is_inscrit = $requete_is_inscrit->fetchAll();

                if (count($resultat_is_inscrit) > 0) {
                    $erreur = "Le client est déjà inscrit à cet évènenment.";
                }
                // sinon user_id existe déja mais il n'est pas inscrit à cet evènement
                else {
                    // requete inscription dans events_users
                    $requete_events_users = $pdo->prepare("
                INSERT INTO events_users (event_id, user_id) VALUES (:event_id, :user_id)
                ON DUPLICATE KEY UPDATE event_id=:event_id, user_id=:user_id;
                ");
                    $requete_events_users->execute([
                        ":event_id" => $event_id,
                        ":user_id" => $id,
                    ]);

                    generate_ticket($pdo, $event_id);


                    header("Location: dashboard_admin.php");
                    exit();
                }
            } else {

                // requete inscription dans users
                $requete_users = $pdo->prepare("
                    INSERT INTO users (email, phone_number, first_name, last_name) 
                    SELECT :email, :phone_number, :first_name, :last_name
                    WHERE NOT EXISTS (
                    SELECT 1 FROM users WHERE email = :email OR phone_number = :phone_number
            );
                    ");

                $requete_users->execute([
                    ":email" => $email,
                    ":phone_number" => $phone_number,
                    ":first_name" => $first_name,
                    ":last_name" => $last_name
                ]);


                $resultat_users = $requete_users->fetchAll(PDO::FETCH_ASSOC);
                $users_res = $resultat_users;
                // var_dump($users_res);

                // requete recup user_id
                $requete_id = $pdo->prepare("
                SELECT id FROM users WHERE email = :email;
                ");

                $requete_id->execute([
                    ":email" => $email
                ]);


                $resultat = $requete_id->fetch(PDO::FETCH_ASSOC);
                $user_id = $resultat['id'];
                // var_dump($resultat);


                // requete inscription dans events_users
                $requete_events_users = $pdo->prepare("
                INSERT INTO events_users (event_id, user_id) VALUES (:event_id, :user_id)
                ON DUPLICATE KEY UPDATE event_id=:event_id, user_id=:user_id;
                ");
                $requete_events_users->execute([
                    ":event_id" => $event_id,
                    ":user_id" => $user_id,
                ]);

                generate_ticket($pdo, $event_id);

                header("Location: dashboard_admin.php");
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
    <link rel="stylesheet" href="style/inscription.css">
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
        <div>
            <a href="logout_admin.php">Déconnexion</a>
        </div>
    </header>

    <h1>Inscrire un client à l'évènement
    </h1>
    <?php if ($erreur !== null) : ?>
        <p style="background: #FAA; color: red; padding: .5rem .75rem">
            <?= $erreur ?>
        </p>
    <?php endif; ?>
    <form method="POST">

        <label for="first_name">Prénom</label>
        <input type="text" id="first_name" name="first_name">

        <label for="last_name">Nom</label>
        <input type="text" id="last_name" name="last_name">

        <label for="email">Email</label>
        <input type="text" id="email" name="email">

        <label for="phone_number">Numero de telephone</label>
        <input type="text" id="phone_number" name="phone_number">
        <input type="hidden" name="name_event" value="<?php echo $name_event; ?>">
        <input type="hidden" name="id" value="<?php echo $event_id; ?>">
        <input type="submit" name="inscrire" value="Inscrire">
    </form>
    <div>
        <a href="dashboard_admin.php">Retour</a>
    </div>
    <footer>
        <p>&copy; 2023 Dauphinois. Tous droits réservés.</p>
    </footer>
</body>

</html>