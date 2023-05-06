<?php
session_start();

$login = $_SESSION["login"];

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header('Location: ../systeme-authentification/users/login.php');
    exit();
}

$pdo = new PDO("mysql:host=localhost:3306;dbname=billetterie_hetic", "root", "");
$erreur = null;
$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

function isBlacklisted($nameEvent)
{
    $blacklist = array(
        'évènement',
        'convention',
        'exposition',
        'concert',
        'tournoi',
        'compétition',
        'avant-première'
    );

    $str = strtolower($nameEvent);

    foreach ($blacklist as $word) {
        if (stripos($str, $word) !== false) {
            return true;
        }
    }
    return false;
}

if ($methode == "POST") {

    $name = filter_input(INPUT_POST, "name_event");
    $date_event = filter_input(INPUT_POST, "date_event");
    $date_create = date("Y-m-d");
    $qrcode = "test";
    $status = "";
    $type = $_POST["radio_event"];

    if (isset($_POST['creer_evenement'])) {
        $nameEvent = $_POST['name_event'];
        if (!empty($nameEvent)) {
            if (isBlacklisted($nameEvent)) {
                $errorMessage = "Le nom de l'évènement ne doit pas contenir les mots suivants : Évènement, Convention, Exposition, Concert, Tournoi, Compétition, Avant-première.";
                echo $errorMessage;
            } else {
                $successMessage = "L'événement a été créé avec succès.";
                echo $successMessage;
            }
        } else {
            $errorMessage = "Veuillez saisir un nom pour l'événement.";
            echo $errorMessage;
        }
    }

    if ($date_create < $date_event) {
        $status = "À venir";
    } else if ($date_create > $date_event) {
        $status = "Passé";
    } else if ($date_create == $date_event) {
        $status = "En cours";
    }

    $requete = $pdo->prepare("
        INSERT INTO events (name, date_event, date_create, creator, qrcode, status, type) VALUES(:name, :date_event, :date_create, :login, :qrcode, :status, :type)
    ");

    $requete->execute([
        ":name" => $name,
        ":date_event" => $date_event,
        ":date_create" => $date_create,
        ":login" => $login,
        ":qrcode" => $qrcode,
        ":status" => $status,
        ":type" => $type,
    ]);

    header('Location: dashboard_user.php');
    exit();
}
?>

<?php
$titre = "Créer un évènement";
include '../tpl/header.php'; ?>

<div class="show_ticket_container">
    <h1>Créer un évènement</h1>

    <form method="post" class="show_ticket_form">

        <div class="show_ticket_div">
            <div class="show_ticket_form_input">
                <label for="name_event">Nom : </label>
                <label for="date_event">Date : </label>
                <label for="radio_event">Type : </label>
            </div>

            <div class="show_ticket_form_input" style="align-items: flex-start;">
                <input type="text" name="name_event" id="name_event" required>
                <input type="date" name="date_event" id="date_event" required>

                <div style="display: flex; flex-direction : column; row-gap: 10px;">
                    <div>
                        <input type="radio" name="radio_event" id="radio_event_1" value="Concert" required>
                        <label for="radio_event_1">Concert</label>
                    </div>

                    <div>
                        <input type="radio" name="radio_event" id="radio_event_2" value="Convention">
                        <label for="radio_event_2">Convention</label>
                    </div>
                    <div>
                        <input type="radio" name="radio_event" id="radio_event_3" value="Exposition">
                        <label for="radio_event_3">Exposition</label>
                    </div>
                    <div>
                        <input type="radio" name="radio_event" id="radio_event_4" value="Tournoi">
                        <label for="radio_event_4">Tournoi</label>
                    </div>
                    <div>
                        <input type="radio" name="radio_event" id="radio_event_5" value="Compétition">
                        <label for="radio_event_5">Compétition</label>
                    </div>
                    <div>
                        <input type="radio" name="radio_event" id="radio_event_6" value="Avant-premiere">
                        <label for="radio_event_6">Avant-première</label>
                    </div>
                </div>

            </div>

        </div>

        <div class="show_ticket_button">
            <input type="submit" name="creer_evenement" value="Créer" class="show_ticket_btn" />
        </div>

    </form>

    <div style="align-self: flex-start;">
        <a href="dashboard_user.php" class="show_ticket_btn">Retour</a>
    </div>

</div>

<?php include '../tpl/footer.php'; ?>