<?php
namespace Mortar\Engine\Build;

class Database {

    private $pdo;

    public function connect() {
        if(!$this->pdo) {
            $this->pdo = new \PDO(DB_LINK, DB_USER, DB_PASS, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false
            ]);
        }
    }

    public function run($query, $parameters = []) {
        $this->connect();
        
        if (!$parameters) {
            return $this->pdo->query($query);
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($parameters);
        return $stmt;
    }
}
