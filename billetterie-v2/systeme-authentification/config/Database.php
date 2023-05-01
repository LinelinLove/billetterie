<?php

class Database{
    private $host = "localhost:3306";
    private $db_name = "billetterie_users";
    private $username = "root";
    private $password = "";
    private $connexion;

    public function getConnection(){
        $this->connexion=null;

        try {
            $this->connexion = new PDO("mysql:host=".$this->host.";dbname=".$this->db_name,$this->username,$this->password);
            $this->connexion->exec("set names utf8");
        }catch (PDOException $exception){
            echo "Erreur lors de la connexion : ". $exception->getMessage();
        }
        return $this->connexion;
    }
}
?>