<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header('Location: login_admin.php');
    exit();
}

$pdo = new PDO("mysql:host=localhost:3306;dbname=billetterie_hetic", "root", "");
$erreur = null;
$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

$status = "Annuler";

$name_event = filter_input(INPUT_POST, "name_event");
$event_id = filter_input(INPUT_POST, "id");
$id_client = filter_input(INPUT_POST, "id_client");

if ($methode == "POST") {
    if (isset($_POST["deleted"])) {

        $requete = $pdo->prepare("
        DELETE FROM events_users WHERE `events_users`.`event_id` = :event_id AND `events_users`.`user_id` = :id_client
    ");

        $requete->execute([
            ":event_id" => $event_id,
            ":id_client" => $id_client
        ]);


        header('Location: dashboard_admin.php');
        exit();
    }
}
?>

<?php include '../tpl/header.php'; ?>

<div class="form">
    <h2>
        Evènement '
        <?= $name_event ?>'
    </h2>
    <div class="cancelled">
        <p class="text_cancelled">
            Êtes-vous sûr de vouloir désinscire cette personne de l'évènement ?
        </p>
    </div>

    <form method="POST" action="event_unsuscribe.php">

        <input type="hidden" name="id_client" value="<?php echo $id_client; ?>" />
        <input type="hidden" name="id" value="<?= $event_id ?>" />
        <input type="hidden" name="name_event" value="<?php echo $name_event; ?>" />
        <div>
            <button>
                <a href="dashboard_admin.php">Retour</a>
            </button>
            <input type="submit" name="deleted" value="Désinscrire" />
        </div>
</div>

</form>
</div>
<?php include '../tpl/footer.php'; ?>