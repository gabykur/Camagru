<?php

namespace App\Models;

class BaseModel {
    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function fetchUserByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":username", $username, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchUserByEmail($email) {
        $sql = "SELECT id FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":email", $email, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
