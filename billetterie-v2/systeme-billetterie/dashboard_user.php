<?php
session_start();
$pdo = new PDO("mysql:host=localhost:3306;dbname=billetterie_hetic;charset=utf8", "root", "");

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {

    header('Location: ../systeme-authentification/users/login.php');
    exit();
}

$login = $_SESSION["login"];

if (isset($_GET['sort'])) {
    $sort = $_GET['sort'];
} else {
    $sort = 'id'; // Tri par défaut
}

if (isset($_GET['order'])) {
    $order = $_GET['order'];
    if ($order == 'ASC') {
        $order = 'DESC';
    } else {
        $order = 'ASC';
    }
} else {
    $order = 'ASC'; // Ordre par défaut
}

$requete_event = $pdo->prepare("
SELECT * FROM events
ORDER BY $sort $order
");

$requete_event->execute();

$evenements = $requete_event->fetchAll(PDO::FETCH_ASSOC);

// recup de l'utilisateur
$requete_admins = $pdo->prepare("
SELECT * FROM admins WHERE admins.login = '$login';
");

$requete_admins->execute();

$user = $requete_admins->fetchAll(PDO::FETCH_ASSOC);

function print_events($evenements, $order, $login)
{
    if (count($evenements) > 0) {
?>
        <table>
            <thead>
                <tr>
                    <th><a href="?sort=id&order=<?php echo $order; ?>">id</a></th>
                    <th><a href="?sort=name&order=<?php echo $order; ?>">Nom</a></th>
                    <th><a href="?sort=type&order=<?php echo $order; ?>">Type</a></th>
                    <th><a href="?sort=date_event&order=<?php echo $order; ?>">Date</a></th>
                    <th><a href="?sort=date_create&order=<?php echo $order; ?>">Date de création</a></th>
                    <th><a href="?sort=creator&order=<?php echo $order; ?>">Auteur</a></th>
                    <th><a href="?sort=status&order=<?php echo $order; ?>">Statut</a></th>
                    <th>Éditions</th>
                    <th>Inscription</th>

                </tr>
            </thead>
            <?php
            foreach ($evenements as $evenement) {
                $date = new DateTime($evenement["date_event"]);
                $date_event_fr = $date->format('d/m/Y');

                $date = new DateTime($evenement["date_create"]);
                $date_create_fr = $date->format('d/m/Y');
            ?>
                <tr>
                    <td><?php echo $evenement["id"]; ?></td>
                    <td>
                        <form method="post" action="event_read.php">
                            <input type="hidden" name="id" value="<?php echo $evenement["id"]; ?>" />
                            <input type="hidden" name="name_event" value="<?php echo $evenement["name"]; ?>" />
                            <input type="submit" name="event_read" class="event_read" value="<?php echo $evenement["name"]; ?>" />
                        </form>
                    </td>
                    <td><?php echo $evenement["type"]; ?></td>
                    <td><?php echo $date_event_fr; ?></td>
                    <td><?php echo $date_create_fr; ?></td>
                    <td><?php echo $evenement["creator"]; ?></td>
                    <td><?php echo $evenement["status"]; ?></td>
                    <td>
                        <?php
                        // Partie modification, annulation, ajouter un client ou retiré un client
                        if ($evenement["creator"] == $login) {
                            if ($evenement["status"] == "À venir") {
                        ?>
                                <div class="UD">
                                    <form method="post" action="event_update.php">
                                        <input type="hidden" name="id" value="<?php echo $evenement["id"]; ?>" />
                                        <input type="hidden" name="name_event" value="<?php echo $evenement["name"]; ?>" />
                                        <input type="hidden" name="date_event" value="<?php echo $evenement["date_event"]; ?>" />
                                        <input type="submit" name="Update" value="Modifier" />
                                    </form>
                                    <form method="post" action="event_delete.php">
                                        <input type="hidden" name="id" value="<?php echo $evenement["id"]; ?>" />
                                        <input type="hidden" name="name_event" value="<?php echo $evenement["name"]; ?>" />
                                        <input type="submit" name="Cancelled" value="Annuler l'événement" />
                                    </form>
                                </div>
                        <?php
                            }
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($evenement["status"] == "À venir" || $evenement["status"] == "En cours") {

                        ?>
                            <form method="post" action="register_customer.php">
                                <input type="submit" name="inscrit" value="Inscrire un client">
                                <input type="hidden" name="name_event" value="<?php echo $evenement["name"]; ?>" />
                                <input type="hidden" name="id" value="<?php echo $evenement["id"]; ?>" />
                            </form>

                    </td>
                </tr>
            <?php
                        }
            ?>
        <?php
            }
        ?>
        </table>
<?php
    } else {
        echo "Pas d'évènement pour le moment !";
    }
}

?>

<?php
$titre = "Dashboard";
include '../tpl/header.php';
?>

<div class="dashboard_user_container">

    <h1>Bonjour <?= $login ?> !</h1>

    <div class="print_events">
        <h2>Liste des évènements :</h2>
        <?= print_events($evenements, $order, $login) ?>
    </div>
    <button class="dashboard_event_create">
        <a href="event_create.php">Créer un évènement</a>
    </button>
</div>

<?php include '../tpl/footer.php'; ?>