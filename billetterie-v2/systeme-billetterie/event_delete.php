<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header('Location: ../systeme-authentification/users/login.php');
    exit(); // Coupe PHP
}

$pdo = new PDO("mysql:host=localhost:3306;dbname=billetterie_hetic", "root", "");
$erreur = null;
$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");
$id = filter_input(INPUT_POST, "id");
$name_event = filter_input(INPUT_POST, "name_event");
$status = "Annuler";

if ($methode == "POST") {
    if (isset($_POST["cancelled"])) {

        $requete = $pdo->prepare("
        UPDATE events SET status = :status WHERE events.id = :id;
    ");

        $requete->execute([
            ":id" => $id,
            ":status" => $status
        ]);
        header('Location: dashboard_user.php');
        exit();
    }
}
?>

<?php
$titre = "Annuler un évènement";
include '../tpl/header.php'; ?>

<div class="show_ticket_container">
    <h2>Annuler un évènement</h2>
    <div class="cancelled">
        <p class="text_cancelled">
            Êtes-vous sûr de vouloir annuler l'évènement "<?= $name_event ?>" ?
        </p>
    </div>

    <form method="POST" action="event_delete.php">
        <input type="hidden" name="id" value="<?= $id ?>" />
        <div>
            <input type="submit" name="cancelled" value="Annuler l'évènement" class="show_ticket_btn" />
        </div>

    </form>

    <div style="align-self: flex-start;">
        <a href="dashboard_user.php" class="show_ticket_btn">Retour</a>
    </div>

</div>
<?php include '../tpl/footer.php'; ?>