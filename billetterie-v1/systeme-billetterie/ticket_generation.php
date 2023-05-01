<?php

function generate_ticket($pdo, $event_id)
{
    function generate_public_code()
    {
        $code = bin2hex(random_bytes(30));
        return $code;
    }
    function generate_private_code()
    {
        $code = bin2hex(random_bytes(10));
        return $code;
    }
    //    $event_id = settype($event_id,"int");
    $add_code = $pdo->prepare("
        INSERT into tickets (event_id,code_public, code_private) VALUES (:event_id,:code_public,:code_private)
    ");

    $add_code->execute([
        ":event_id" => $event_id,
        ":code_public" => generate_public_code(),
        ":code_private" => generate_private_code(),
    ]);
}
