<?php
session_start();

$pdo = new PDO("mysql:host=localhost:3306;dbname=billetterie_hetic", "root", "");
$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");
// require "show_ticket_client.php";
require "ticket_generation.php";

// Cette variable, si elle contient un texte, montre qu'il y a eu une erreur
$erreur = null;
if ($methode == "POST") {
    // Ici, la personne vient de soumettre le formulaire
    $login = filter_input(INPUT_POST, "login");
    $_SESSION["login"] = $login;
    $password = filter_input(INPUT_POST, "password");

    // Récupérer l'ensemble des utilisateurs (ou juste un utilisateur)
    // qui ont le pseudo machin voire même avec le mot de passe bidule
    $requete = $pdo->prepare("
        SELECT * FROM admins
        WHERE login = :login
    ");
    $requete->execute([
        ":login" => $login
    ]);
    $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

    if (password_verify($password, $utilisateur["password"])) {
        // identifiants sont corrects :
        // 1. On définit la clef loggedin à true dans la session : permet de savoir si ce navigateur est bien passé par le formulaire de connexion et a bien pu se connecter
        $_SESSION["loggedin"] = true;

        // 2. On redirige, quand on redirige, c'est une bonne pratique de couper PHP directement après.
        header('Location: dashboard_admin.php');
        exit();
    } else {
        $erreur = "Identifiants incorrects !";
    }
}


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

function print_events($evenements, $order)
{
    if (count($evenements) > 0) {
?>
        <table>
            <thead>
                <tr>
                    <th><a href="?sort=name&order=<?php echo $order; ?>">Nom</a></th>
                    <th><a href="?sort=type&order=<?php echo $order; ?>">Type</a></th>
                    <th><a href="?sort=date_event&order=<?php echo $order; ?>">Date</a></th>
                    <th><a href="?sort=date_create&order=<?php echo $order; ?>">Date de création</a></th>
                    <th><a href="?sort=creator&order=<?php echo $order; ?>">Auteur</a></th>
                    <th><a href="?sort=status&order=<?php echo $order; ?>">Statut</a></th>
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
                    <td>
                        <?php echo $evenement["name"]; ?>
                    </td>
                    <td><?php echo $evenement["type"]; ?></td>
                    <td><?php echo $date_event_fr; ?></td>
                    <td><?php echo $date_create_fr; ?></td>
                    <td><?php echo $evenement["creator"]; ?></td>
                    <td><?php echo $evenement["status"]; ?></td>
                </tr>

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

<div class="print_events">
    <h2>Liste des évènements :</h2>
    <div>
        <?= print_events($evenements, $order) ?>
    </div>
</div>

<?php include '../tpl/footer.php'; ?>