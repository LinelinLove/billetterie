<?php
// Etape 1. On démarre la session
session_start();
// Etape 2. On supprime toutes les données de la session
// Très mauvaise idée (risque de bugs et autres) :
// $_SESSION["loggedin"] = false;
// Mauvaise idée (risque de bugs et galère à maintenir :
// unset($_SESSION["loggedin"]);
// Ca ne marche pas comme on s'y attend :
// unset($_SESSION);
// Seule bonne idée :
session_destroy();
// Etape 3. On redirige vers le formulaire de connexion.
header('Location: ../systeme-authentification/users/login.php');
exit();