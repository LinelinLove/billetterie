<?php

session_start();
$username = $_SESSION['username'];
$publicCode = $_SESSION['publicCode'];

$pdo = new PDO("mysql:host=localhost:3306;dbname=billetterie_hetic","root","");

// $username = filter_input(INPUT_GET,"username");
// $publicCode = filter_input(INPUT_GET,"publicCode");

$request_ticket = $pdo->prepare("
SELECT * 
FROM tickets 
INNER JOIN `users`
    WHERE tickets.code_public = :public_code
    AND `users`.last_name = :username
");
$request_ticket->execute([
    ":username"=>$username,
    ":public_code"=>$publicCode
]);

$tickets = $request_ticket->fetchAll(PDO::FETCH_ASSOC);

function Verification($tickets, $username)
{
    if (count($tickets) > 0) {
        
        // Définition de l'en-tête HTTP
        header("HTTP/1.1 200 OK");

        // Définition du type 
        header("Content-Type: text/html; charset=UTF-8");

        //CSS pour le fond vert
        $css = "<style>body { background-color: green; }</style>";

        // Construction du corps de la page
        $body = "<html><head>$css</head><body><h1>Votre ticket est valide</h1></body></html>";

        // Affichage de la page
        echo $body;
    }  else {
        
        // Définition de l'en-tête HTTP
        header("HTTP/1.1 401 Unauthorized");

        // Définition du type 
        header("Content-Type: text/html; charset=UTF-8");

        //CSS pour le fond rouge
        $css = "<style>body { background-color: red; }</style>";

        // Construction du corps de la page
        $body = "<html><head>$css</head><body><h1>Bonjour " . $username . "</h1></body>
        
        <p>Votre ticket n'est pas valide</p>
        
        </html>";

        // Affichage de la page
        echo $body;
    }
       
}


Verification($tickets, $username);
