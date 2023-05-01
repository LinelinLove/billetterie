<?php
date_default_timezone_set('Europe/Paris');
$pdo = new PDO("mysql:host=localhost:3306;dbname=billetterie_hetic", "root", "");
$erreur = null;
$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

// Supprimer les tokens expirés
$pdo->query("DELETE FROM tokens WHERE expiration < NOW()");

if ($methode === "POST") {
    $token = bin2hex(random_bytes(32));
    $timestamp_expiration = time() + 60;
    $expiration = date('Y-m-d H:i:s', $timestamp_expiration);

    $requete = $pdo->prepare("
        INSERT INTO tokens (token, expiration) VALUES(:token, :expiration)
    ");

    try {
        $requete->execute([
            ":token" => $token,
            ":expiration" => $expiration
        ]);
    } catch (PDOException $e) {
        $erreur = "Error: " . $e->getMessage();
    }
}

if ($erreur === null) {
    $stmt = $pdo->query("SELECT token, expiration FROM tokens ORDER BY id DESC LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $token = $result['token'];
    $expiration = $result['expiration'];

    if (strtotime($expiration) < time()) {
        $erreur = "Le token a expiré. Un nouveau a été généré.";
        $stmt = $pdo->query("DELETE FROM tokens WHERE expiration < NOW()");
        $requete = $pdo->prepare("
            INSERT INTO tokens (token, expiration) VALUES(:token, :expiration)
        ");

        try {
            $requete->execute([
                ":token" => bin2hex(random_bytes(32)),
                ":expiration" => date('Y-m-d H:i:s', time() + 60)
            ]);
        } catch (PDOException $e) {
            $erreur .= " Error: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation</title>
</head>

<body>
    <h1>Générer un token</h1>

    <?php if ($erreur !== null) { ?>
        <p><?php echo $erreur; ?></p>
    <?php } ?>

    <form method="post">
        <button type="submit">Générer un nouveau token</button>
    </form>

    <?php if ($erreur === null) { ?>
        <p>Token généré : <?php echo $token; ?></p>
        <p>Date d'expiration : <?php echo $expiration; ?></p>
    <?php } ?>
</body>

</html>
