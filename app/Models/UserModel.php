<?php

namespace App\Models;

class UserModel extends BaseModel{
    private $pdo;

    public function __construct($pdo) {
        parent::__construct($pdo);
    }

    public function updateUsername($username, $userId) {
        $sql = "UPDATE users SET username = :username WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":username", $username, \PDO::PARAM_STR);
        $stmt->bindParam(":id", $userId, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateEmail($email, $activationCode, $userId) {
        $sql = "UPDATE users SET new_email = :email, activation_code = :activation_code WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":email", $email, \PDO::PARAM_STR);
        $stmt->bindParam(":activation_code", $activationCode, \PDO::PARAM_STR);
        $stmt->bindParam(":id", $userId, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Password modification queries
    public function fetchUserPassword($username) {
        $sql = "SELECT password FROM users WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':username', $username, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function updateUserPassword($username, $newPassword) {
        $sql = "UPDATE users SET password = :password WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array(':password' => $newPassword, ':username' => $username));
        return $stmt->rowCount() > 0;
    }

    // Account deletion queries
    public function fetchUserById($userId) {
        $sql = "SELECT id, password FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function deletePhotosByUser($userId) {
        $query = $this->pdo->prepare("SELECT * FROM pictures WHERE id_user = :id_user");
        if ($query->execute(array(':id_user' => $userId))) {
            return $query->fetchAll(\PDO::FETCH_ASSOC);
        }
        return [];
    }

    public function deleteUserComments($userId) {
        $query = $this->pdo->prepare("DELETE FROM comments WHERE id_user = :id_user");
        $query->bindParam(':id_user', $userId, \PDO::PARAM_INT);
        return $query->execute();
    }

    public function deleteUserLikes($userId) {
        $query = $this->pdo->prepare("SELECT id_img FROM likes WHERE id_user = :id_user");
        $query->execute(array(':id_user' => $userId));
        $res = $query->fetchAll(\PDO::FETCH_ASSOC);
        if ($res) {
            foreach ($res as $likedPhoto) {
                $this->pdo->query("UPDATE pictures SET likes = likes - 1 WHERE id_img = " . intval($likedPhoto['id_img']));
            }
        }
        $query = $this->pdo->prepare("DELETE FROM likes WHERE id_user = :id_user");
        $query->bindParam(':id_user', $userId, \PDO::PARAM_INT);
        return $query->execute();
    }

    public function deleteUserPictures($userId) {
        $query = $this->pdo->prepare("DELETE FROM pictures WHERE id_user = :id_user");
        $query->bindParam(':id_user', $userId, \PDO::PARAM_INT);
        return $query->execute();
    }

    public function deleteUserAccount($userId) {
        $query = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        $query->bindParam(':id', $userId, \PDO::PARAM_INT);
        return $query->execute();
    }

    // Notifications queries
    public function getNotificationSetting($userId) {
        $query = $this->pdo->prepare("SELECT notif FROM users WHERE id = :id");
        $query->bindParam(':id', $userId, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    public function updateNotificationSetting($userId, $notif) {
        $query = $this->pdo->prepare("UPDATE users SET notif = :notif WHERE id = :id");
        $query->bindParam(':notif', $notif, \PDO::PARAM_INT);
        $query->bindParam(':id', $userId, \PDO::PARAM_INT);
        return $query->execute();
    }

    // Email verification queries
    public function fetchUserByEmailAndActivationCode($email, $activationCode) {
        $sql = 'SELECT id, new_email FROM users WHERE new_email = :email AND activation_code = :activation_code';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':activation_code', $activationCode);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function updateEmailAndClearActivationCode($userId) {
        $sql = 'UPDATE users SET email = new_email, new_email = NULL, activation_code = "" WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }

    // Fetch user photos
    public function fetchUserPhotos($userId) {
        $stmt = $this->pdo->prepare("SELECT img, id_img FROM pictures WHERE id_user = :id_user ORDER BY date DESC");
        $stmt->bindParam(":id_user", $userId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Delete photos from the database
    public function deletePhotosFromDatabase($delId) {
        $tables = ['comments', 'likes', 'pictures'];
        foreach ($tables as $table) {
            $stmt = $this->pdo->prepare("DELETE FROM $table WHERE id_img IN ($delId)");
            $stmt->execute();
        }
    }
}
?>