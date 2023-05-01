<?php
session_start();
$pdo = new PDO("mysql:host=localhost:3306;dbname=billetterie_hetic;charset=utf8", "root", "");

// Première chose, si la personne n'est pas connectée, c'est dangereux de lui afficher la page sécurisée. On la renvoie vers le login.
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Dans ce cas là, la personne n'est pas connectée, on la redirige.
    header('Location: ../systeme-authentification/users/login.php');
    exit(); // Coupe PHP
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

// echo var_dump($user);

function print_events($evenements, $order, $login)
{
    if (count($evenements) > 0) {
?>
        <table>
            <thead>
                <tr>
                    <th><a href="?sort=id&order=<?php echo $order; ?>">id</a></th>
                    <th><a href="?sort=name&order=<?php echo $order; ?>">Nom de l'évènement</a></th>
                    <th><a href="?sort=type&order=<?php echo $order; ?>">Type de l'évènement</a></th>
                    <th><a href="?sort=date_event&order=<?php echo $order; ?>">Date de l'évènement</a></th>
                    <th><a href="?sort=date_create&order=<?php echo $order; ?>">Date de création</a></th>
                    <th><a href="?sort=creator&order=<?php echo $order; ?>">Auteur</a></th>
                    <th><a href="?sort=status&order=<?php echo $order; ?>">Status</a></th>
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
                            <form method="post" action="inscription.php">
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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/dashoboard_admin.css">
    <title>Votre espace</title>
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
            <a href="../systeme-authentification/users/logout.php">Déconnexion</a>
        </div>
    </header>

    <div class="body_event">
        <h1>Bonjour '<?= $login ?>' !</h1>

        <div>
            <button>
                <a href="create_event.php" class="button_event">Créer un évènement</a>
            </button>
        </div>
        <div class="print_events">
            <h2>Liste des évènements :</h2>
            <?= print_events($evenements, $order, $login) ?>
        </div>
    </div>
    <footer>
        <p>&copy; 2023 Dauphinois. Tous droits réservés.</p>
    </footer>
</body>

</html>