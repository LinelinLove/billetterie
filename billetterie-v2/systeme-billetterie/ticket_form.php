<?php
$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");

if($methode == "GET"){

  if (isset($_GET['username']) && isset($_GET['privateCode'])) {
    $username = $_GET['username'];
    $privateCode = $_GET['privateCode'];

    if (!preg_match("/^[a-zA-Z\s]+$/", $username)) {
        // Le champ nom contient des caractères non autorisés
        echo "Le champ nom ne doit contenir que des lettres et des espaces.";
    } else {
        // Le champ nom est valide
        echo "Le champ nom est valide.";
    }
  
  
    if (!preg_match("/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,10}$/", $privateCode)) {
        // Le champ code du billet contient des caractères non autorisés
        echo "Le champ code du ticket doit contenir entre 8 et 10 lettres ou chiffres, avec au moins une lettre et un chiffre.";
    } else {
        // Le champ code du billet est valide
        echo "Le champ code du ticket est valide.";
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
    <form method="GET" action="">

      <label for="username">Nom </label>  
      <input type="text" name="username" id="username">

      <label for="privateCode"> Code public billet</label>
      <input type="text" name="privateCode" id="privateCode">
      
      <input type="submit" value="Confirmer">
    
    </form>    

</body>
</html>
