<?php

$pdo = new PDO("mysql:host=localhost:3306;dbname=billetterie_hetic", "root", "");

$username = filter_input(INPUT_POST, "last_name");
$privateCode = filter_input(INPUT_POST, "pTicketId");

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

<?php
$titre = "Votre billet";
include '../tpl/header.php';
?>

<section class="show_ticket_container">

    <h1>Votre billet</h1>

    <form method="POST" action="show_ticket_client.php" class="show_ticket_form">

        <div class="show_ticket_div">
            <div class="show_ticket_form_input">
                <label for="last_name">Nom de famille :</label>
                <label for="pTicketId">Identifiant privé du billet :</label>
            </div>

            <div class="show_ticket_form_input">
                <input type="text" id="last_name" name="last_name">
                <input type="text" id="pTicketId" name="pTicketId" maxlength="10">
            </div>
        </div>

        <div class="show_ticket_button">
            <input type="submit" name="form_ticket" value="Afficher mon billet" class="show_ticket_btn">
        </div>

    </form>

</section>

<?php include '../tpl/footer.php'; ?>