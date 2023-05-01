<?php 


// $links = [
//     "../../systeme-billetterie/index.php" => "Accueil",
//     "../../systeme-billetterie/show_ticket.php" => "Votre billet",
//     "login.php" => "Admin",
//     "../../systeme-billetterie/dashboard_user.php" => "Dashboard Admin",
//     "../../systeme-billetterie/dashboard_customer.php" => "Dashboard Client",
//     "logout.php" => "Déconnexion"
// ];

function nav_login()
{
    $links = [
        "../../systeme-billetterie/dashboard_user.php" => "Dashboard",
        "logout.php" => "Déconnexion"
    ];
    return $links;
}

function nav_logout()
{
    $links = [
        "../../systeme-billetterie/index.php" => "Accueil",
        "../../systeme-billetterie/show_ticket.php" => "Votre billet",
        "../../systeme-billetterie/dashboard_customer.php" => "Dashboard",
        "login.php" => "Connexion",
    ];
    return $links;
}

$links_login = nav_login();
$links_logout = nav_logout();

?>



<nav>
    <ul>
        <?php
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            foreach ($links_login as $link => $texte) :
        ?>
                <li>
                    <a href="<?= $link ?>"><?= $texte ?></a>
                </li>
            <?php endforeach ?>


            <?php } else {
            foreach ($links_logout as $link => $texte) : ?>
                <li>
                    <a href="<?= $link ?>"><?= $texte ?></a>
                </li>
        <?php endforeach;
        } ?>
    </ul>
</nav>