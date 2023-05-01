<?php

class Users
{
    private $connexion;
    private $table = "users";

    public $id;
    public $login;
    public $password;
    public $first_name;
    public $last_name;
    public $email;
    public $phone_number;

    /**
     * @param $db
     */
    public function __construct($db)
    {
        $this->connexion = $db;
    }

    /**
     * Add a user
     *
     * @return void
     */
    public function add_user()
    {

        $sql = "INSERT INTO " . $this->table . " 
        SET login=:login,password=:password,email=:email, phone_number=:phone_number, first_name=:first_name, last_name=:last_name";

        $query = $this->connexion->prepare($sql);

        $this->login = htmlspecialchars(strip_tags($this->login));
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->phone_number = htmlspecialchars(strip_tags($this->phone_number));
        $this->email = htmlspecialchars(strip_tags($this->email));

        $query->bindParam(":login", $this->login);
        $query->bindParam(":password", $this->password);
        $query->bindParam(":first_name", $this->first_name);
        $query->bindParam(":last_name", $this->last_name);
        $query->bindParam(":phone_number", $this->phone_number);
        $query->bindParam(":email", $this->email);

        if ($query->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Read users
     *
     * @return void
     */
    public function read_users()
    {

        $sql = "SELECT * FROM " . $this->table;

        $query = $this->connexion->prepare($sql);

        $query->execute();

        return $query;
    }

    /**
     * Read one user
     *
     * @return void
     */
    public function read_user()
    {

        $sql = "SELECT * FROM " . $this->table . " WHERE " . $this->table . ".id = ?";

        $query = $this->connexion->prepare($sql);

        $query->bindParam(1, $this->id);

        $query->execute();

        $datas = $query->fetch(PDO::FETCH_ASSOC);

        $this->login = $datas['login'];
        $this->password = $datas['password'];
        $this->first_name = $datas['first_name'];
        $this->last_name = $datas['last_name'];
        $this->phone_number = $datas['phone_number'];
        $this->email = $datas['email'];
    }

    /**
     * Read users
     *
     * @return void
     */
    public function update_user()
    {

        $sql = "UPDATE " . $this->table . " SET email=:email, phone_number=:phone_number, first_name=:first_name, =:last_name";

        $query = $this->connexion->prepare($sql);

        $this->login = htmlspecialchars(strip_tags($this->login));
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->phone_number = htmlspecialchars(strip_tags($this->phone_number));
        $this->email = htmlspecialchars(strip_tags($this->email));


        $query->bindParam(":login", $this->login);
        $query->bindParam(":password", password_hash($this->password, PASSWORD_DEFAULT));
        $query->bindParam(":first_name", $this->first_name);
        $query->bindParam(":last_name", $this->last_name);
        $query->bindParam(":phone_number", $this->phone_number);
        $query->bindParam(":email", $this->email);

        if ($query->execute()) {
            return true;
        }
    }

    /**
     * Delete a user
     *
     * @return void
     */
    public function del_user()
    {

        $sql = "DELETE FROM " . $this->table . " WHERE id = ?";

        $query = $this->connexion->prepare($sql);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $query->bindParam(1, $this->id);

        if ($query->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Log a user
     *
     * @return void
     */
    public function login()
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE login = :login";

        $query = $this->connexion->prepare($sql);

        $query->bindParam(":login", $this->login);

        $query->execute();

        return $query;
    }
}
