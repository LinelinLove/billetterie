<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header('Location: ../systeme-authentification/users/login.php');
    exit();
}

$pdo = new PDO("mysql:host=localhost:3306;dbname=billetterie_hetic", "root", "");
$erreur = null;
$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

$status = "Annuler";

$name_event = filter_input(INPUT_POST, "name_event");
$event_id = filter_input(INPUT_POST, "id");
$id_client = filter_input(INPUT_POST, "id_client");

$client_last_name = filter_input(INPUT_POST, "client_last_name");
$client_first_name = filter_input(INPUT_POST, "client_first_name");

if ($methode == "POST") {
    if (isset($_POST["deleted"])) {

        $requete = $pdo->prepare("
        DELETE FROM events_users WHERE `events_users`.`event_id` = :event_id AND `events_users`.`user_id` = :id_client
    ");

        $requete->execute([
            ":event_id" => $event_id,
            ":id_client" => $id_client
        ]);


        header('Location: dashboard_user.php');
        exit();
    }
}
?>

<?php 
$titre = "Désinscrire une personne";
include '../tpl/header.php'; ?>

<div class="show_ticket_container">
    <h2>
        Evènement '<?= $name_event ?>'
    </h2>
    <div class="cancelled">
        <p class="text_cancelled">
            Êtes-vous sûr de vouloir désinscire "<?= $client_last_name ?> <?= $client_first_name ?>" de l'évènement ?
        </p>
    </div>

    <form method="POST" action="event_unsuscribe.php" class="show_ticket_form">

        <input type="hidden" name="id_client" value="<?php echo $id_client; ?>" />
        <input type="hidden" name="id" value="<?= $event_id ?>" />
        <input type="hidden" name="name_event" value="<?php echo $name_event; ?>" />

        <div>
            <input type="submit" name="deleted" value="Désinscrire" class="show_ticket_btn"/>
        </div>


    </form>

    <div style="align-self: flex-start">
        <a href="dashboard_user.php" class="show_ticket_btn">Retour</a>
    </div>
</div>
<?php include '../tpl/footer.php'; ?>