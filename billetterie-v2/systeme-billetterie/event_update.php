<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Dans ce cas là, la personne n'est pas connectée, on la redirige.
    header('Location: ../systeme-authentification/users/login.php');
    exit(); // Coupe PHP
}

$pdo = new PDO("mysql:host=localhost:3306;dbname=billetterie_hetic", "root", "");
$erreur = null;
$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

$id = filter_input(INPUT_POST, "id");
$name = filter_input(INPUT_POST, "name_event");
$date_event = filter_input(INPUT_POST, "date_event");

$name_update = filter_input(INPUT_POST, "name_event_update");
$date_event_update = filter_input(INPUT_POST, "date_event_update");

if ($methode == "POST") {
    if (isset($_POST["updated"])) {
        $requete = $pdo->prepare("
        UPDATE events SET name = :name_update, date_event = :date_event_update WHERE events.id = :id;

    ");

        $requete->execute([
            ":id" => $id,
            ":name_update" => $name_update,
            ":date_event_update" => $date_event_update,
        ]);
        header('Location: dashboard_user.php');
        exit();
    }
}
?>

<?php 
$titre = "Modifier un évènement";
include '../tpl/header.php'; ?>

<div class="show_ticket_container">
    <h2>Modifier un évènement</h2>

    <form method="post" action="event_update.php" class="show_ticket_form">


        <div class="show_ticket_div">

            <div class="show_ticket_form_input">
                <label for="name_event_update">Nom : </label>
                <label for="date_event_update">Date de l'événement : </label>
            </div>

            <div class="show_ticket_form_input" style="align-items: flex-start">
                <input type="text" name="name_event_update" id="name_event_update" value="<?= $name ?>" required>
                <input type="date" name="date_event_update" id="date_event_update" value="<?= $date_event ?>" required>
            </div>
        </div>

        <input type="hidden" name="id" value="<?= $id ?>" />
        <input type="hidden" name="name_event" value="<?= $name ?>" />
        <input type="hidden" name="date_event" value="<?= $date_event ?>" />

        <div class="show_ticket_button">
            <input type="submit" name="updated" value="Mettre à jour" class="show_ticket_btn" />
        </div>
        
    </form>
    
    <div style="align-self: flex-start">
        <a class="show_ticket_btn" href="dashboard_user.php">Retour</a>
    </div>

</div>

<?php include '../tpl/footer.php'; ?>