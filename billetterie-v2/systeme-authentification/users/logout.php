<?php

session_start();
include_once "../config/Database.php";
include_once "../models/Users.php";

$database = new Database();
$db = $database->getConnection();

$sql="DELETE FROM `tokens` WHERE `tokens`.`token` = :token";
$query= $db->prepare($sql);
$query->execute([
    ":token"=>$_SESSION["token"]
]);

session_destroy();
header('Location: login.php');
exit();