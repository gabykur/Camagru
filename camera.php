<?php
require("config/database.php");
date_default_timezone_set('Europe/Paris');
session_start();

if (empty($_SESSION['loggedin'])) {
    header('Location: /user/login.php');
    exit;
}

function fetchStickers($pdo) {
    $query = $pdo->query("SELECT id_sticker, path FROM stickers");
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function fetchUserPhotos($pdo, $user_id) {
    $query = $pdo->prepare("SELECT img, id_img FROM pictures WHERE id_user = :id_user ORDER BY date DESC");
    $query->bindParam(":id_user", $user_id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function validatePhotoAndSticker($photo, $sticker_id) {
    if (empty($photo) || empty($sticker_id)) {
        echo "[ERROR] Invalid photo or sticker.";
        exit;
    }
}

function savePhotoToFile($photo_data) {
    $photo_parts = explode(',', $photo_data);
    $data = base64_decode($photo_parts[1]);
    $file_path = 'public/upload/' . date("YmdHis") . '.png';
    file_put_contents($file_path, $data);
    return $file_path;
}

function fetchSticker($pdo, $sticker_id) {
    $query = $pdo->prepare("SELECT * FROM stickers WHERE id_sticker = :sticker_id");
    $query->bindParam(":sticker_id", $sticker_id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}

function applyStickerToPhoto($photo_path, $sticker_path) {
    $photo_copy = imagecreatefrompng($photo_path);
    $sticker_copy = imagecreatefrompng($sticker_path);
    $resized_mask = imagecreatetruecolor(265, 250);
    $trans_color = imagecolorallocatealpha($resized_mask, 0, 0, 0, 127);
    imagefill($resized_mask, 0, 0, $trans_color);
    imagealphablending($sticker_copy, true);
    imagesavealpha($sticker_copy, true);
    $src_x = imagesx($sticker_copy);
    $src_y = imagesy($sticker_copy);
    imagecopyresampled($resized_mask, $sticker_copy, 0, 0, 0, 0, 265, 250, $src_x, $src_y);
    imagecopy($photo_copy, $resized_mask, 0, 0, 0, 0, 265, 250);
    imagepng($photo_copy, $photo_path);
    imagedestroy($photo_copy);
}

function fetchUser($pdo, $username) {
    $query = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $query->bindParam(":username", $username, PDO::PARAM_STR);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}

function savePhotoToDatabase($pdo, $user_id, $photo_path) {
    $query = $pdo->prepare("INSERT INTO pictures (id_user, img) VALUES (:id_user, :img)");
    $query->bindParam(":id_user", $user_id, PDO::PARAM_INT);
    $query->bindParam(":img", $photo_path, PDO::PARAM_STR);
    if ($query->execute()) {
        echo "[SUCCESS] Picture added to db";
        header('Location: camera.php');
        exit;
    } else {
        $error_info = $query->errorInfo();
        echo "[ERROR] Failed to insert photo into database: " . $error_info[2];
    }
}

if (isset($_POST['tookAphoto'])) {
    $photo_data = $_POST['photo'];
    $sticker_id = $_POST['sticker'];

    validatePhotoAndSticker($photo_data, $sticker_id);
    $photo_path = savePhotoToFile($photo_data);
    $sticker = fetchSticker($pdo, $sticker_id);

    if ($sticker && file_exists($sticker['path'])) {
        applyStickerToPhoto($photo_path, $sticker['path']);
        $username = $_SESSION['username'];
        $user = fetchUser($pdo, $username);
        if ($user) {
            savePhotoToDatabase($pdo, $user['id'], $photo_path);
        } else {
            echo "[ERROR] User not found.";
        }
    } else {
        echo "[ERROR] Sticker file not found.";
    }
}

$stickers = fetchStickers($pdo);
$user_photos = fetchUserPhotos($pdo, $_SESSION['id']);
?>

<?php ob_start(); ?>
<div class="background">
    <div id="camera">
        <div id="video_div">
            <div id="live_video">
                <img src="public/stickers/poop.png" id="overlay">
            </div>
            <video id="video"></video>
        </div>
        <div id="sticker_div">
            <?php foreach ($stickers as $sticker): ?>
                <img src="<?= $sticker['path'] ?>" class="stickerImg" data-id="<?= $sticker['id_sticker'] ?>" onclick="selectSticker(this)">
            <?php endforeach; ?>
        </div>
        <form method="POST" action="" onsubmit="takePhoto();">
            <input id="photo" name="photo" type="hidden" value="">
            <input id="sticker" name="sticker" type="hidden" value="">
            <input id="snap" style="display:none;" type="submit" name="tookAphoto" value="">
        </form>
        <canvas style="display:none" id="canvas" width="640" height="480"></canvas>
        <div id='camera_gallery'>
            <?php foreach ($user_photos as $photo): ?>
                <img src="<?= $photo['img'] ?>" id='photo' id='<?= $photo['id_img'] ?>'>
            <?php endforeach; ?>
        </div>
        <div><p id="text-camera">No camera? No problem! <a style="color: #EFB4E4;" href="upload.php"><b>Click here :)</b></a></p></div>
        <canvas style="display:none" id="canvasCopy" width="640" height="480"></canvas>
    </div>
</div><br/><br/>
<script src="/public/js/camera.js"></script>
<?php $view = ob_get_clean(); ?>
<?php require("template.php"); ?>
