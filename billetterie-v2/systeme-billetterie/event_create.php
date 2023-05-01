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

<?php include '../tpl/header.php'; ?>

<form method="post">

    <fieldset class="event">

        <legend>
            Créer votre propre événement !
        </legend>

        <div>
            <label for="name_event">Nom : </label>
            <input type="text" name="name_event" id="name_event" required>
        </div>

        <div>
            <label for="date_event">Date de l'événement : </label>
            <input type="date" name="date_event" id="date_event" required>
        </div>

        <div>
            <label for="radio_event_1">
                <input type="radio" name="radio_event" id="radio_event_1" value="Concert" required>Concert
            </label>
            <label for="radio_event_2">
                <input type="radio" name="radio_event" id="radio_event_2" value="Convention">Convention
            </label>
            <label for="radio_event_3">
                <input type="radio" name="radio_event" id="radio_event_3" value="Exposition">Exposition
            </label>
            <label for="radio_event_4">
                <input type="radio" name="radio_event" id="radio_event_4" value="Tournoi">Tournoi
            </label>
            <label for="radio_event_5">
                <input type="radio" name="radio_event" id="radio_event_5" value="Compétition">Compétition
            </label>
            <label for="radio_event_6">
                <input type="radio" name="radio_event" id="radio_event_6" value="Avant-premiere">Avant-première
            </label>

        </div>
        <div>
            <button for="button_return">
                <a href="dashboard_user.php" class="button_event" name="button_return">Retour</a>
            </button>
            <button>
                <type="submit" name="creer_evenement">Créer
            </button>
        </div>
    </fieldset>
</form>

<?php include '../tpl/footer.php'; ?>