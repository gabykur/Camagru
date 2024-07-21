<?php 
require("../config/database.php");
require_once "../vendor/autoload.php";
session_start();

if (empty($_SESSION['loggedin'])) {
    header('Location: ../index.php');
    exit;
}

$message = "";
$message_err = "";

function testInput($data) {
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

function fetchUserPhotos($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT img, id_img FROM pictures WHERE id_user = :id_user ORDER BY date DESC");
    $stmt->bindParam(":id_user", $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function deleteFilesFromFilesystem($files) {
    foreach ($files as $file) {
        unlink("../" . $file['img']);
    }
}

function deletePhotosFromDatabase($pdo, $delId) {
    $tables = ['comments', 'likes', 'pictures'];
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("DELETE FROM $table WHERE id_img IN ($delId)");
        $stmt->execute();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    if (isset($_POST['check']) && !empty($_POST['check'])) {
        $checkbox = array_map('intval', $_POST['check']);
        $delId = implode(",", $checkbox);
        $filesToDelete = fetchUserPhotos($pdo, $_SESSION['id']);
        $filesToDelete = array_filter($filesToDelete, function($photo) use ($checkbox) {
            return in_array($photo['id_img'], $checkbox);
        });
        deleteFilesFromFilesystem($filesToDelete);
        deletePhotosFromDatabase($pdo, $delId);
        $message = "Selected photos have been deleted successfully.";
    } else {
        $message_err = "Please choose photos to delete.";
    }
}
?>

<?php ob_start(); ?>
<div class="background galleryB">
    <div id="test">
        <h2 id="title" style="padding-top:0;text-shadow: 4px 2px 1px #67e8a6;">Hey Kitty</h2>
        <div id="account">
            <nav id="account_nav">
                <a id="EdPro" href="account.php">Edit Profile</a>
                <a id="EdPwd" href="modify_password.php">Edit Password</a>
                <a id="DelPho" href="delete_photos.php">Delete Photos</a>
                <a id="DelAcc" href="delete_account.php">Delete Account</a>
                <a id="Notif" href="notifications.php">Notifications</a>
            </nav>
            <article>
                <div class="loginForm accountForm DelPho">      
                    <h2 id="subTitle">Delete Photos</h2>
                    <span style="color:green"><?php echo htmlspecialchars($message); ?></span>
                    <span style="color:red"><?php echo htmlspecialchars($message_err); ?></span>
                    <div id="deletePhotos">      
                        <form method="POST" action="">
                            <?php 
                                $photos = fetchUserPhotos($pdo, $_SESSION['id']);
                                foreach ($photos as $photo): ?>
                                    <div id='img'>
                                        <img src='../<?php echo htmlspecialchars($photo['img']); ?>'>
                                        <input type='checkbox' id='check_del' name='check[]' value='<?php echo intval($photo['id_img']); ?>'>
                                    </div>
                                <?php endforeach; ?>
                            <div class="loginForm accountForm" style="background:none; box-shadow:none">
                                <input type="submit" id="saveBtt" style="margin-top: 11px;font-size: 24px;margin-bottom:7px" name="delete" value="Delete">          
                            </div>
                        </form>
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>

<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>
