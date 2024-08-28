<?php

class DB {
    private $dbh;  // Verbinding met de database
    protected $stmt;  // Het huidige statement

    public function __construct($db, $host = "localhost", $user = "root", $pass = "") {
        try {
            $this->dbh = new PDO("mysql:host=$host;dbname=$db;", $user, $pass);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection error: " . $e->getMessage());
        }
    }

    public function execute($query, $args = null): PDOStatement|false {
        $stmt = $this->dbh->prepare($query);
        $stmt->execute($args);
        return $stmt;
    }

    public function lastId(): int {
        return $this->dbh->lastInsertId();
    }

    // Voeg een functie toe om de PDO-verbinding te krijgen
    public function getConnection() {
        return $this->dbh;
    }
}

$myDb = new DB('examen_herkansing');
$pdo = $myDb->getConnection(); // Voeg deze regel toe om $pdo beschikbaar te maken
?>
