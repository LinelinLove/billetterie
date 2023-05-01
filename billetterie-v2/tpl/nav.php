<?php include_once '../inc/functions.php';
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