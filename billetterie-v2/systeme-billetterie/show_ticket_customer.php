<?php

// session_start();

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
?>
        <div class="print_events">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Nom de l'évènement</th>
                        <th>Date de l'évènement</th>
                    </tr>
                </thead>
                <?php foreach ($tickets as $ticket) {

                    $date = new DateTime($ticket["date_event"]);
                    $date_event_fr = $date->format('d/m/Y');

                    // $date = new DateTime($ticket["date_create"]);
                    // $date_create_fr = $date->format('d/m/Y');

                ?>
                    <tr>
                        <td><?= $ticket["last_name"] ?></td>
                        <td><?= $ticket["first_name"] ?></td>
                        <td><?= $ticket["name"] ?></td>
                        <td><?= $date_event_fr ?></td>
            </table>

            <a href='show_ticket.php' class="show_ticket_btn" style="align-self: flex-start">Retour</a>

        </div>

    <?php }
            } else { ?>

    <div class="print_events">
        <p>
            Pas de ticket à ce nom ou bien le code privé est invalide.
        </p>

        <a href='show_ticket.php' class="show_ticket_btn" style="align-self: flex-start">Retour</a>
    </div>

<?php }
        }

        if (isset($_POST["form_ticket"])) {

            $titre = "Votre billet";
            include '../tpl/header.php';

            showTickets($tickets);

            include '../tpl/footer.php';
        }
?>