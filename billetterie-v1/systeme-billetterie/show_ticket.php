<?php

// session_start();

$pdo = new PDO("mysql:host=localhost:3306;dbname=billetterie_hetic", "root", "");

$username = filter_input(INPUT_POST, "last_name");
$privateCode = filter_input(INPUT_POST, "pTicketId");
//$username = "Master";
//$privateCode = "code1";


$request_ticket = $pdo->prepare("
SELECT u.last_name, u.first_name, u.email, e.name, e.date_event, e.date_create, e.qrcode 
    FROM tickets 
    INNER JOIN events_users 
    ON tickets.event_id = events_users.event_id 
    INNER JOIN users as u 
    ON events_users.user_id = u.id 
    INNER JOIN `events` as e 
    ON events_users.event_id = e.id 
    WHERE u.last_name = :username AND tickets.code_private= :private_code");
$request_ticket->execute(array(
    ":username" => $username,
    ":private_code" => $privateCode
));
$tickets = $request_ticket->fetchAll(PDO::FETCH_ASSOC);

//echo var_dump($tickets);

function showTickets($tickets)
{
    if (count($tickets) > 0) {
        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Nom de l'utilisateur</th>";
        echo "<th>Nom de l'évènement</th>";
        echo "<th>Date de l'évènement</th>";
        echo "<th>Date de création</th>";
        echo "<th>QRCode</th>";
        echo "</tr>";
        echo "</thead>";
        foreach ($tickets as $ticket) {
            echo "<tr>";
            echo "<td>" . $ticket["last_name"] . "</td>";
            echo "<td>" . $ticket["name"] . "</td>";
            echo "<td>" . $ticket["date_event"] . "</td>";
            echo "<td>" . $ticket["date_create"] . "</td>";
            echo "<td>" . $ticket["qrcode"] . "</td>";
        }
        echo ("
            </table>
            <button>
            <a href='dashboard_admin.php'>Retour</a>
            </button>"
        );
    }
}

if (isset($_POST["form_ticket"])) {

    if ($username === null && $privateCode === null) {
        header("location : ../systeme-authentification/users/login.php");
    } else {
        echo showTickets($tickets);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/show_ticket.css">
    <title>Document</title>
</head>

<body>
    <header>
        <h1>Dauphinois</h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="show_ticket.php">Billets</a></li>
                <li><a href="../systeme-authentification/users/login.php">Admin</a></li>
                <li><a href="dashboard_admin.php">Dashboard</a></li>
            </ul>
        </nav>
    </header>
    <div>
        <h2>Entrer votre billet</h2>
        <form method="POST" action="show_ticket_client.php">
            <label for="last_name">Identifiant</label>
            <input type="text" id="last_name" name="last_name">
            <label for="pTicketId">Identifiant privé du billet</label>
            <input type="text" id="pTicketId" name="pTicketId">

            <input type="submit" name="form_ticket" value="Afficher mon billet">

        </form>
    </div>
    <footer>
        <p>&copy; 2023 Dauphinois. Tous droits réservés.</p>
    </footer>
</body>

</html>