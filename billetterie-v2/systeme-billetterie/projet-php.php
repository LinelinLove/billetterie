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
            $qrCodeData = 'http://localhost/billetterie/verification.php?data=' . urlencode($jsonData);

            // Charger la librairie QRcode
            require_once 'phpqrcode/qrlib.php';

            // Générer le QR code et l'afficher dans la page
            echo "<img src='" . QRcode::png($qrCodeData, false, QR_ECLEVEL_L, 10) . "' />";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    form {
        /* centre la forme */
        margin: 0 auto;
        /* encadrement */
        border: 2px solid black;
        border-radius: 5px;
        padding: 10px;
        width: 50%;
        height: 200px;
    }
</style>

<body>
    <form method="GET" action="Projet-PHP.php">

        <label for="username">Nom </label>
        <input type="text" name="username" id="username" required>

        <label for="publicCode"> Code public billet</label>
        <input type="text" name="publicCode" id="publicCode" maxlength="30" required>

        <input type="submit" name="Confirmer" value="Confirmer">

    </form>

</body>

</html>
