<?php
namespace Mortar\Engine\Build;

class Database {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function run($query, $parameters = []) {
        if (!$parameters) {
            return $this->pdo->query($query);
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($parameters);
        return $stmt;
    }
}
