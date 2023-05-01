<?php

function nav_login()
{
    $links = [
        "dashboard_user.php" => "Dashboard",
        "../systeme-authentification/users/logout.php" => "Déconnexion"
    ];
    return $links;
}

function nav_logout()
{
    $links = [
        "index.php" => "Accueil",
        "show_ticket.php" => "Votre billet",
        "dashboard_customer.php" => "Dashboard",
        "../systeme-authentification/users/login.php" => "Connexion",
    ];
    return $links;
}