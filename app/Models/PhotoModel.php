<?php

namespace App\Models;

class PhotoModel extends BaseModel {
    protected $pdo;

    public function __construct($pdo) {
        parent::__construct($pdo);
    }

    public function getPhotoCount() {
        $query = $this->pdo->query('SELECT id_img FROM pictures');
        return $query->rowCount();
    }

    public function getPhotos($start, $photos_per_page) {
        $stmt = $this->pdo->prepare("SELECT pictures.id_img, pictures.img, pictures.date, pictures.likes, users.username, COUNT(comments.id_img) AS nb_comment
                                    FROM pictures
                                    LEFT JOIN comments ON (pictures.id_img = comments.id_img) 
                                    INNER JOIN users ON pictures.id_user = users.id 
                                    GROUP BY pictures.id_img 
                                    ORDER BY pictures.date DESC 
                                    LIMIT :start, :photos_per_page");
        $stmt->bindParam(':start', $start, \PDO::PARAM_INT);
        $stmt->bindParam(':photos_per_page', $photos_per_page, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function fetchPhoto($id_photo) {
        $query = $this->pdo->prepare("SELECT pictures.id_img, pictures.img, pictures.date, users.username 
                                      FROM pictures 
                                      INNER JOIN users ON pictures.id_user = users.id 
                                      WHERE pictures.id_img = ?");
        $query->execute([$id_photo]);
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addLike($id_user, $id_img) {
        $query = $this->pdo->prepare("INSERT INTO likes(id_user, id_img) VALUES(:id_user, :id_img)");
        $query->bindParam(':id_user', $id_user);
        $query->bindParam(':id_img', $id_img);
        if ($query->execute()) {
            $update = $this->pdo->prepare("UPDATE pictures SET likes = likes + 1 WHERE id_img = :id_img");
            $update->bindParam('id_img', $id_img);
            $update->execute();
        }
    }

    public function removeLike($id_user, $id_img) {
        $query = $this->pdo->prepare("DELETE FROM likes WHERE id_user = :id_user AND id_img = :id_img");
        $query->bindParam(':id_user', $id_user);
        $query->bindParam(':id_img', $id_img);
        if ($query->execute()) {
            $update = $this->pdo->prepare("UPDATE pictures SET likes = likes - 1 WHERE id_img = :id_img");
            $update->bindParam('id_img', $id_img);
            $update->execute();
        }
    }

    public function countLikes($id_img) {
        $query = $this->pdo->prepare("SELECT count(id_img) AS likes FROM likes WHERE id_img = :id_img");
        $query->bindParam(':id_img', $id_img);
        $query->execute();
        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchUserLikeStatus($id_img, $id_user) {
        $query = $this->pdo->prepare("SELECT id_like FROM likes WHERE id_img = :id_img AND id_user = :id_user");
        $query->bindParam(':id_img', $id_img);
        $query->bindParam(':id_user', $id_user);
        $query->execute();
        return $query->fetchColumn();
    }

    public function fetchComments($id_img) {
        $query = $this->pdo->prepare("SELECT comments.id_user, comments.comment, comments.id_comment, users.username
                                      FROM comments
                                      INNER JOIN users ON comments.id_user = users.id
                                      WHERE comments.id_img = :id_img
                                      ORDER BY comments.date ASC");
        $query->bindParam(':id_img', $id_img);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function insertComment($id_img, $id_user, $comment) {
        $query = $this->pdo->prepare("INSERT INTO comments(id_img, id_user, comment) VALUES(:id_img, :id_user, :comment)");
        $query->bindParam(':id_img', $id_img);
        $query->bindParam(':id_user', $id_user);
        $query->bindParam(':comment', $comment);
        return $query->execute();
    }

    public function deleteComment($id_comment, $id_user) {
        $query = $this->pdo->prepare("DELETE FROM comments WHERE id_comment = :id_comment AND id_user = :id_user");
        $query->bindParam(':id_comment', $id_comment);
        $query->bindParam(':id_user', $id_user);
        return $query->execute();
    }

    public function fetchPhotoUser($id_img) {
        $query = $this->pdo->prepare("SELECT email, notif, username 
                                      FROM pictures 
                                      JOIN users ON pictures.id_user = users.id 
                                      WHERE pictures.id_img = :id_img");
        $query->bindParam(':id_img', $id_img);
        $query->execute();
        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchStickers() {
        $query = $this->pdo->query("SELECT id_sticker, path FROM stickers");
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function fetchUserPhotos($userId) {
        $query = $this->pdo->prepare("SELECT img, id_img FROM pictures WHERE id_user = :id_user ORDER BY date DESC");
        $query->bindParam(":id_user", $userId, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function fetchSticker($stickerId) {
        $query = $this->pdo->prepare("SELECT * FROM stickers WHERE id_sticker = :sticker_id");
        $query->bindParam(":sticker_id", $stickerId, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function savePhotoToDatabase($userId, $photoPath) {
        $query = $this->pdo->prepare("INSERT INTO pictures (id_user, img) VALUES (:id_user, :img)");
        $query->bindParam(":id_user", $userId, \PDO::PARAM_INT);
        $query->bindParam(":img", $photoPath, \PDO::PARAM_STR);
        return $query->execute();
    }
}
?>
