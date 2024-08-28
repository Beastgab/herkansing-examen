<?php
require 'db.php';

class User {
    private $dbh;
    private $tableUser = 'users';

    public function __construct(DB $dbh) 
    {
        $this->dbh = $dbh;
    }

    public function hash($password) : string {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function getAllUsers() : array 
    {
        return $this->dbh->execute("SELECT * from $this->tableUser")->fetchAll();
    }

    public function getOneUser($id) : array 
    {
        return $this->dbh->execute("SELECT * from $this->tableUser where id = ?", [$id])->fetch();
    }

    public function insertUser($email, $password) : PDOStatement | false
    {
        return $this->dbh->execute("INSERT INTO $this->tableUser (email, password) values (?,?)", [$email, $this->hash($password)]);
    }

    public function editUser($email, $password, $id) : PDOStatement 
    {
        return $this->dbh->execute("UPDATE $this->tableUser SET email = ?, password = ? where id = ?", [$email,  $this->hash($password), $id]);
    }
    public function deleteUser($id) : PDOStatement 
    {
        return $this->dbh->execute("DELETE FROM $this->tableUser where id = ?", [$id]);
    }
    public function login($email) : array | bool 
    {
        return $this->dbh->execute("SELECT * FROM $this->tableUser WHERE email = ?",[$email])->fetch();
    }
}
$user = new User($myDb);
?>
