<?php
session_start();

$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

if ($methode == "GET" && isset($_GET["Confirmer"])) {

    if (isset($_GET['username']) && isset($_GET['publicCode'])) {
        $_SESSION['username'] = $_GET['username'];
        $_SESSION['publicCode'] = $_GET['publicCode'];
        $username = $_GET['username'];
        $publicCode = $_GET['publicCode'];

        if (!preg_match("/^[a-zA-Z\s]+$/", $username)) {
            // Le champ nom contient des caractères non autorisés
            echo " Le champ nom ne doit contenir que des lettres et des espaces.";
        } else if (isset($_GET['publicCode']) && strlen($_GET['publicCode']) < 30) {
            echo "Le champ code du ticket doit contenir 30 lettres ou chiffres.";
        } else {
            // Le champ code du billet est valide
            // echo "Le champ code du ticket est valide.";

            // Générer le code QR avec les données du formulaire
            $data = [
                'username' => $username,
                'publicCode' => $publicCode
            ];

            // Encode les données en JSON pour les transmettre dans l'URL du QR code
            $jsonData = json_encode($data);
            $qrCodeData = 'http://localhost/billetterie/systeme-billetterie/verification.php?data=' . urlencode($jsonData);

            // Charger la librairie QRcode
            require_once 'phpqrcode/qrlib.php';

            // Générer le QR code et l'afficher dans la page

            // $qrcode = QRcode::png($qrCodeData, false, QR_ECLEVEL_L, 10);
            QRcode::png($qrCodeData, null, QR_ECLEVEL_L, 10);
            $qrcode = ob_get_clean();
            ob_start();

            $titre = "QR Code";

            include '../tpl/header.php'; ?>

            <div class="show_ticket_container">
                <h1>Voici votre QR Code</h1>
                <img src="data:image/png;base64,<?= base64_encode($qrcode) ?>" alt="qrcode">

                <a href='generate_qrcode.php' class="show_ticket_btn" style="align-self: center">Retour</a>

            </div>

            <?php include '../tpl/footer.php';
            // Récupérer le contenu HTML généré
            $contenuHtml = ob_get_clean();

            // Remplacer le contenu de la page par le nouveau contenu
            echo $contenuHtml;

            // Terminer le script
            exit();

            ?>
<?php

        }
    }
}
?>


<?php
$titre = "Générer votre QR Code";

include '../tpl/header.php'; ?>
<div class="show_ticket_container">

    <h1>Génération de votre QR Code</h1>

    <form method="GET" action="generate_qrcode.php" class="show_ticket_form">

        <div class="show_ticket_div">
            <div class="show_ticket_form_input">
                <label for="username">Nom de famille : </label>
                <label for="publicCode">Code public billet : </label>
            </div>

            <div class="show_ticket_form_input">
                <input type="text" name="username" id="username" required>
                <input type="text" name="publicCode" id="publicCode" maxlength="30" required>
            </div>
        </div>

        <div class="show_ticket_button">

            <input type="submit" name="Confirmer" value="Confirmer" class="show_ticket_btn">
        </div>

    </form>
</div>

<?php include '../tpl/footer.php'; ?>