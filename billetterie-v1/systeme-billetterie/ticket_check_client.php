<?php

session_start();

$pdo = new PDO("mysql:host=localhost:3306;dbname=billetterie_hetic","root","");

$username = filter_input(INPUT_POST,"username");
$privateCode = filter_input(INPUT_POST,"pTicketId");
//$username = "John";
//$privateCode = "code1";


$request_ticket = $pdo->prepare("
    SELECT * 
    FROM ticket 

");
$request_ticket->execute(array(
    ":username"=>$username,
    ":private_code"=>$privateCode
));

$tickets = $request_ticket->fetchAll(PDO::FETCH_ASSOC);

function Verification ($tickets)
{
    if (count($tickets) > 0) {
        // Le billet existe    
        echo "<html><head><title>ticket existant</title></head><body style='background-color:green;'></body></html>";
}  else {
        // Le billet n'existe pas
        echo "<html><head><title>Ticket non existant</title></head><body style='background-color:red;'></body></html>";
}
       
}
?>