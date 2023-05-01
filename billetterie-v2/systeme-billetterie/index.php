<?php
$titre = "Accueil";
include '../tpl/header.php';

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
} else {

    header('Location: dashboard_user.php');
    exit();
}

?>

<main>
    <section class="index_container">
        <figure class="index_figure">
            <img src="assets/genshin_2023.png">
        </figure>
        <p class="index_bouton">
            <a href="dashboard_customer.php" class="btn">Voir l'évènement</a>
        </p>
    </section>
</main>

<?php include '../tpl/footer.php'; ?>