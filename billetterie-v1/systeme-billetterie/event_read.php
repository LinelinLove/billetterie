<?php
session_start();

// Première chose, si la personne n'est pas connectée, c'est dangereux de lui afficher la page sécurisée. On la renvoie vers le login.
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Dans ce cas là, la personne n'est pas connectée, on la redirige.
    header('Location: ../systeme-authentification/users/login.php');
    exit(); // Coupe PHP
}

$login = $_SESSION["login"];


$pdo = new PDO("mysql:host=localhost:3306;dbname=billetterie_hetic", "root", "");
$name_event = filter_input(INPUT_POST, "name_event");
$event_id = filter_input(INPUT_POST, "id");

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

//recup l'id de l'event
$requete_read_inscrit = $pdo->prepare("
SELECT * FROM events_users WHERE event_id = '$event_id';
");

$requete_read_inscrit->execute();
$id_event = $requete_read_inscrit->fetchAll(PDO::FETCH_ASSOC);

$requete_afficher_inscrit = $pdo->prepare("
SELECT *
FROM users
INNER JOIN events_users
ON users.id = events_users.user_id
WHERE event_id = '$event_id';
");

$requete_afficher_inscrit->execute();

$affichage = $requete_afficher_inscrit->fetchAll(PDO::FETCH_ASSOC);

function print_inscrit($id_event, $affichage, $event_id, $name_event)
{
    if (count($id_event) > 0) {
?>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>E-mail</th>
                    <th>Téléphone</th>
                    <th>Désinscrire</th>
                </tr>
            </thead>
            <?php
            foreach ($affichage as $affiche) {
            ?>
                <tr>
                    <td>
                        <?php echo $affiche["last_name"] ?>
                    </td>
                    <td>
                        <?php echo $affiche["first_name"] ?>
                    </td>
                    <td>
                        <?php echo $affiche["email"] ?>
                    </td>
                    <td>
                        <?php echo $affiche["phone_number"];
                        ?>
                    </td>
                    <td>
                        <form method="post" action="event_unsuscribe.php">
                            <!-- id client -->
                            <input type="hidden" name="id_client" value="<?php echo $affiche["id"]; ?>" />
                            <input type="hidden" name="name_event" value="<?php echo $name_event; ?>" />
                            <input type="hidden" name="id" value="<?php echo $event_id; ?>" />
                            <input type="submit" name="Cancelled" value="Annuler l'inscription" />
                        </form>
                    </td>
                </tr>
            <?php } ?>
    <?php } else {
        echo "Personne d'inscrit pour cet évènement";
    }
} ?>


    <!-- html -->

    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style/event_read.css">
        <title>Liste des inscrit</title>
    </head>

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

    <body>
        <div>
            <a href="dashboard_admin.php">Retour</a>
        </div>
        <h1>Bonjour
            <?= $login ?> !
        </h1>

        <div class="print_events">
            <h2>Liste des inscrit à
                <?php echo $name_event ?>:
            </h2>

            <?php
            print_inscrit($id_event, $affichage, $event_id, $name_event);
            ?>

        </div>
        <footer>
            <p>&copy; 2023 Dauphinois. Tous droits réservés.</p>
        </footer>
    </body>

    </html>