<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Dans ce cas là, la personne n'est pas connectée, on la redirige.
    header('Location: login_admin.php');
    exit(); // Coupe PHP
}

$pdo = new PDO("mysql:host=localhost:3306;dbname=billetterie_hetic", "root", "");
$erreur = null;
$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");
$id = filter_input(INPUT_POST, "id");
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
        header('Location: dashboard_admin.php');
        exit();
    }
}
?>

<?php include '../tpl/header.php'; ?>

<div class="form">
    <h2>Annuler un évènement</h2>
    <div class="cancelled">
        <p class="text_cancelled">
            Êtes-vous sûr de vouloir annuler cet évènement <?= $id ?> ?
        </p>
    </div>

    <form method="POST" action="event_delete.php">
        <input type="hidden" name="id" value="<?= $id ?>" />
        <div>
            <button>
                <a href="dashboard_admin.php">Retour</a>
            </button>
            <input type="submit" name="cancelled" value="Annuler" />
        </div>
</div>

</form>
</div>
<?php include '../tpl/footer.php'; ?>