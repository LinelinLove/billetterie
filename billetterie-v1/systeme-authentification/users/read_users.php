<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers");

include_once "../config/Database.php";
include_once "../models/Users.php";

$database = new Database();
$db = $database->getConnection();

$user = new Users($db);

$stmt = $user->read_users();

if ($stmt->rowCount()>0){

    $usersTable = [];
    $usersTable["users"] = [];

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $tUser = [
            "id"=>$id,
            "login"=>$login,
            "password"=>$password,
            "first_name"=>$first_name,
            "last_name"=>$last_name,
            "phone_number"=>$phone_number,
            "email"=>$email,
        ];

        $usersTable["users"][] = $tUser;
    }
    http_response_code(200);

    echo json_encode($usersTable);
}
