<?php

namespace App\Models;

class AuthModel extends BaseModel{
    protected $pdo;

    public function __construct($pdo) {
        parent::__construct($pdo);
        $dotenv = Dotenv::createImmutable(__DIR__ . '../');
        $dotenv->load();
    }

    public function insertUser($username, $email, $password, $activation_code) {
        $sql = "INSERT INTO users (username, email, password, activation_code, user_status, token, notif, account_locked, account_locked_until)
                VALUES (:username, :email, :password, :activation_code, :user_status, :token, :notif, :account_locked, :account_locked_until)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":username", $username, \PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, \PDO::PARAM_STR);
        $stmt->bindParam(":password", $password, \PDO::PARAM_STR);
        $stmt->bindParam(":activation_code", $activation_code, \PDO::PARAM_STR);

        $user_status = 'not verified';
        $token = '';
        $notif = 1;
        $account_locked = 0;
        $account_locked_until = NULL;

        $stmt->bindParam(":user_status", $user_status, \PDO::PARAM_STR);
        $stmt->bindParam(":token", $token, \PDO::PARAM_STR);
        $stmt->bindParam(":notif", $notif, \PDO::PARAM_INT);
        $stmt->bindParam(":account_locked", $account_locked, \PDO::PARAM_INT);
        $stmt->bindParam(":account_locked_until", $account_locked_until, \PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function fetchUserByActivationCode($activation_code) {
        $query = "SELECT * FROM users WHERE activation_code = :activation_code";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':activation_code', $activation_code, \PDO::PARAM_STR);
        $statement->execute();
        return $statement->fetch(\PDO::FETCH_ASSOC);
    }

    public function verifyUser($username) {
        $update_query = "UPDATE users SET user_status = 'verified', activation_code = '' WHERE username = :username";
        $statement = $this->pdo->prepare($update_query);
        $statement->bindParam(':username', $username, \PDO::PARAM_STR);
        return $statement->execute();
    }

    public function fetchTokenByUsername($username) {
        $query = $this->pdo->prepare('SELECT token FROM users WHERE username = :username');
        $query->bindParam(':username', $username, \PDO::PARAM_STR);
        $query->execute();
        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchToken($token) {
        $query = $this->pdo->prepare('SELECT token FROM users WHERE token = :token');
        $query->bindParam(':token', $token, \PDO::PARAM_STR);
        $query->execute();
        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    public function updatePassword($new_password, $token) {
        $new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_pass = "UPDATE users SET password = :password WHERE token = :token";
        $stmt = $this->pdo->prepare($update_pass);
        $stmt->execute([
            ':password' => $new_password,
            ':token' => $token
        ]);
        return $stmt->rowCount();
    }

    public function logFailedAttempt($user_id, $ip_address) {
        $sql = "INSERT INTO login_attempts (user_id, ip_address, attempt_time) VALUES (:user_id, :ip_address, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, \PDO::PARAM_INT);
        $stmt->bindParam(":ip_address", $ip_address, \PDO::PARAM_STR);
        $stmt->execute();
    }

    public function countFailedAttempts($user_id, $ip_address) {
        $sql = "SELECT COUNT(*) FROM login_attempts WHERE user_id = :user_id AND ip_address = :ip_address AND attempt_time > (NOW() - INTERVAL 1 HOUR)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, \PDO::PARAM_INT);
        $stmt->bindParam(":ip_address", $ip_address, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function lockAccount($user_id) {
        $sql = "UPDATE users SET account_locked = 1, account_locked_until = (NOW() + INTERVAL 1 HOUR) WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":id", $user_id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function unlockAccount($user_id) {
        $sql = "UPDATE users SET account_locked = 0, account_locked_until = NULL WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":id", $user_id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function resetFailedAttempts($user_id) {
        $sql = "DELETE FROM login_attempts WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, \PDO::PARAM_INT);
        $stmt->execute();
    }
}
