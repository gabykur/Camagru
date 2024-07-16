<?php
require("config/database.php");
date_default_timezone_set('Europe/Paris');
session_start();

if (empty($_SESSION['loggedin'])) {
    header('Location: /user/login.php');
    exit;
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
    $query = $pdo->prepare("INSERT INTO picture (id_user, img) VALUES (:id_user, :img)");
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

function fetchStickers($pdo) {
    $query = $pdo->query("SELECT * FROM stickers");
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_POST['submit'])) {
    ini_set('max_execution_time', '60');

    $photo_data = $_POST['photo'];
    $sticker_id = $_POST['sticker'];

    validatePhotoAndSticker($photo_data, $sticker_id);

    $photo_path = savePhotoToFile($photo_data);

    $sticker = fetchSticker($pdo, $sticker_id);
    echo "[DEBUG] Sticker query result: " . print_r($sticker, true) . "<br>";

    if ($sticker && file_exists($sticker['path'])) {
        applyStickerToPhoto($photo_path, $sticker['path']);

        $username = $_SESSION['username'];
        $user = fetchUser($pdo, $username);
        echo "[DEBUG] User query result: " . print_r($user, true) . "<br>";

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
?>

<?php ob_start(); ?>
<div class="background">
    <div id="camera">
        <div id="video_div">
            <div id="live_video">
                <img src="public/stickers/poop.png" id="overlay">
            </div> 
            <img id='upload_img' width="100%" height="100%"/>
        </div>
        <div id="sticker_div">
            <?php foreach ($stickers as $sticker): ?>
                <img src="<?= $sticker['path']; ?>" class="stickerImg" data-id="<?= $sticker['id_sticker']; ?>" onclick="stickerSelector(this)">
            <?php endforeach; ?>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" id="upload" onsubmit="uploadPhoto()">
            <label for="uploadPic">Load file</label>
            <input type="file" name="uploadPic" id="uploadPic" style="display:none" accept="image/*">
            <input id="photo" name="photo" type="hidden" value="">
            <input id="sticker" name="sticker" type="hidden" value="1">
            <button type="submit" name="submit" id="uploadBtt" value="">Save</button>
        </form>

        <canvas style="display:none" id="canvas" width="640" height="480"></canvas>
    </div>
    <canvas style="display:none" id="canvasCopy" width="640" height="480"></canvas>
</div><br/><br/>
<script src="/public/js/upload.js"></script>
<?php $view = ob_get_clean(); ?>
<?php require("template.php"); ?>
