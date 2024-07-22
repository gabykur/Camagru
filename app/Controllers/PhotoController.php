<?php

namespace App\Controllers;

use App\Models\PhotoModel;
use App\Utils\InputValidator;
use App\Utils\SessionHelper;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

class PhotoController {
    private $photoModel;

    public function __construct($pdo) {
        $this->photoModel = new PhotoModel($pdo);
    }

    public function addLikeComment() {
        SessionHelper::checkLoggedIn();

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $id_photo = $_GET['id'] ?? null;
        $comment = $_POST['comment'] ?? '';
        $comment = InputValidator::testInput($comment);
        $likes = $this->photoModel->countLikes($id_photo);

        if ($id_photo) {
            $photo = $this->photoModel->fetchPhoto($id_photo);
            if (empty($photo)) {
                header("Location: /index.php");
                exit;
            }
        } else {
            header("Location: /index.php");
            exit;
        }

        if (isset($_GET['like'])) {
            $this->photoModel->addLike($_SESSION['id'], $_GET['like']);
            header("Location: /photo/addLikeComment?id=" . $id_photo);
            exit;
        }

        if (isset($_GET['dislike'])) {
            $this->photoModel->removeLike($_SESSION['id'], $_GET['dislike']);
            header("Location: /photo/addLikeComment?id=" . $id_photo);
            exit;
        }

        if (!empty($comment)) {
            if ($this->photoModel->insertComment($id_photo, $_SESSION['id'], $comment)) {
                $photo_user = $this->photoModel->fetchPhotoUser($id_photo);
                if ($photo_user['notif'] == 1 && $_SESSION['username'] != $photo_user['username']) {
                    $this->sendNotificationEmail($photo_user['email'], $photo_user['username'], $_SESSION['username'], $comment);
                }
            }
            header("Location: /photo/addLikeComment?id=" . $id_photo);
            exit;
        }

        if (isset($_POST['delete_comment']) && isset($_POST['comment_id'])) {
            $comment_id = $_POST['comment_id'];
            $this->photoModel->deleteComment($comment_id, $_SESSION['id']);
            header("Location: /photo/addLikeComment?id=" . $id_photo);
            exit;
        }

        $photo = $photo[0];  // Since fetchPhoto returns an array
        $likeStatus = $this->photoModel->fetchUserLikeStatus($id_photo, $_SESSION['id']);
        $all_comments = $this->photoModel->fetchComments($id_photo);

        $pageTitle = "Photo";
        $view = '../Views/photo/addLikeComment.php';
        require('../Views/template.php');
    }

    private function sendNotificationEmail($email, $username, $commenter, $comment) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USERNAME'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = $_ENV['SMTP_ENCRYPTION'];
            $mail->Port = $_ENV['SMTP_PORT'];

            $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'New Comment';
            $mail->Body = "
                Hey $username,<br><br>
                You have received a new comment on your photo from:<br><br>
                <b>$commenter</b>: <i>\"$comment\"</i><br><br>
                Best regards,<br>
                The Catgram Team
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log("Failed to send email: " . $mail->ErrorInfo);
        }
    }

    public function camera() {
        SessionHelper::checkLoggedIn();
        $stickers = $this->photoModel->fetchStickers();
        $userPhotos = $this->photoModel->fetchUserPhotos($_SESSION['id']);
        $pageTitle = 'Camera';
        $view = 'photo/camera.php';
        require_once __DIR__ . '/../../app/Views/template.php';;
    }

    public function upload() {
        SessionHelper::checkLoggedIn();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ini_set('max_execution_time', '60');

            $photoData = $_POST['photo'];
            $stickerId = $_POST['sticker'];

            $this->validatePhotoAndSticker($photoData, $stickerId);

            $photoPath = $this->savePhotoToFile($photoData);

            $sticker = $this->photoModel->fetchSticker($stickerId);
            if ($sticker && file_exists($sticker['path'])) {
                $this->applyStickerToPhoto($photoPath, $sticker['path']);

                $username = $_SESSION['username'];
                $user = $this->photoModel->fetchUserByUsername($username);

                if ($user) {
                    $this->savePhotoToDatabase($user['id'], $photoPath);
                } else {
                    echo "[ERROR] User not found.";
                }
            } else {
                echo "[ERROR] Sticker file not found.";
            }
        }

        $stickers = $this->photoModel->fetchStickers();
        $pageTitle = 'Upload';
        $view = 'photo/upload.php';
        require_once __DIR__ . '/../../app/Views/template.php';;
    }

    private function validatePhotoAndSticker($photo, $stickerId) {
        if (empty($photo) || empty($stickerId)) {
            echo "[ERROR] Invalid photo or sticker.";
            exit;
        }
    }

    private function savePhotoToFile($photoData) {
        $photoParts = explode(',', $photoData);
        $data = base64_decode($photoParts[1]);
        $filePath = 'public/upload/' . date("YmdHis") . '.png';
        file_put_contents($filePath, $data);
        return $filePath;
    }

    private function applyStickerToPhoto($photoPath, $stickerPath) {
        $photoCopy = imagecreatefrompng($photoPath);
        $stickerCopy = imagecreatefrompng($stickerPath);
        $resizedMask = imagecreatetruecolor(265, 250);
        $transColor = imagecolorallocatealpha($resizedMask, 0, 0, 0, 127);
        imagefill($resizedMask, 0, 0, $transColor);
        imagealphablending($stickerCopy, true);
        imagesavealpha($stickerCopy, true);
        $srcX = imagesx($stickerCopy);
        $srcY = imagesy($stickerCopy);
        imagecopyresampled($resizedMask, $stickerCopy, 0, 0, 0, 0, 265, 250, $srcX, $srcY);
        imagecopy($photoCopy, $resizedMask, 0, 0, 0, 0, 265, 250);
        imagepng($photoCopy, $photoPath);
        imagedestroy($photoCopy);
    }

    private function savePhotoToDatabase($userId, $photoPath) {
        if ($this->photoModel->savePhotoToDatabase($userId, $photoPath)) {
            echo "[SUCCESS] Picture added to db";
            header('Location: /photo/camera');
            exit;
        } else {
            echo "[ERROR] Failed to insert photo into database.";
        }
    }
}
?>
