<?php

session_start();
$username = $_SESSION['username'];
$publicCode = $_SESSION['publicCode'];

$pdo = new PDO("mysql:host=localhost:3306;dbname=billetterie_hetic", "root", "");

$request_ticket = $pdo->prepare("
SELECT * 
FROM tickets 
INNER JOIN `users`
    WHERE tickets.code_public = :public_code
    AND `users`.last_name = :username
");
$request_ticket->execute([
    ":username" => $username,
    ":public_code" => $publicCode
]);

$tickets = $request_ticket->fetchAll(PDO::FETCH_ASSOC);

function verification($tickets, $username)
{
    if (count($tickets) > 0) {

        $css = "<style>body { background-color: green; } .verification {
            display: flex;
            flex-direction: column;
            row-gap: 10px;
            margin: 50px;
        }
        }</style>";

        $body = "<head>$css</head><body><div class='verification'><h1>Bonjour " . $username . "</h1>
        <p>Votre ticket est valide</p></div></body>";

        // Définition de l'en-tête HTTP
        header("HTTP/1.1 200 OK");

        // Définition du type 
        header("Content-Type: text/html; charset=UTF-8");

        $titre = "QR Code";
        include '../tpl/header.php';
        echo $body;
        include '../tpl/footer.php';
    } else {

        $css = "<style>body { background-color: red; } .verification {
            display: flex;
            flex-direction: column;
            row-gap: 10px;
            margin: 50px;
        }
        }</style>";

        $body = "<head>$css</head><body><div class='verification'><h1>Bonjour " . $username . "</h1>
        <p>Votre ticket n'est pas valide</p></div></body>";

        // Définition de l'en-tête HTTP
        header("HTTP/1.1 401 Unauthorized");

        // Définition du type 
        header("Content-Type: text/html; charset=UTF-8");

        $titre = "QR Code";
        include '../tpl/header.php';
        echo $body;
        include '../tpl/footer.php';
    }
}


verification($tickets, $username);
