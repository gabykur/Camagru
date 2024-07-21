<?php
session_start();
require("../config/database.php");

if (empty($_SESSION['loggedin'])) {
    header('Location: ../index.php');
    exit;
}

$message = "";

function getNotificationSetting($pdo, $user_id) {
    $query = $pdo->prepare("SELECT notif FROM users WHERE id = :id");
    $query->bindParam(':id', $user_id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}

function updateNotificationSetting($pdo, $user_id, $notif) {
    $query = $pdo->prepare("UPDATE users SET notif = :notif WHERE id = :id");
    $query->bindParam(':notif', $notif, PDO::PARAM_INT);
    $query->bindParam(':id', $user_id, PDO::PARAM_INT);
    return $query->execute();
}

$user_id = $_SESSION['id'];
$data = getNotificationSetting($pdo, $user_id);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    // Check if the checkbox is set
    $notif = isset($_POST['notif']) ? 1 : 0;

    // Update notification setting in the database
    if (updateNotificationSetting($pdo, $user_id, $notif)) {
        $message = "Updated ;)";
        header("Refresh: 2");
    } else {
        $message = "Failed to update.";
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
                <div style="max-height: 705px;" id="a">
                    <div class="loginForm accountForm Notif">      
                        <h2 id="subTitle">Notifications</h2>
                        <span style="color:green"><?php echo htmlspecialchars($message); ?></span>
                        <form action="" method="post">  
                            <label id="notiflabel">
                                <input id="checkbox" type="checkbox" name="notif" <?php if (isset($data['notif']) && $data['notif'] === 1): ?>checked="checked"<?php endif; ?>>
                                <p id="pNot">Set up notifications on comments</p>
                            </label>
                            <div class="loginForm accountForm" style="background:none; box-shadow:none">
                                <input type="submit" id="saveBtt" style="width: 29%;font-size: 24px;" name="update" value="Update">          
                            </div>   
                        </form>
                    </div><br>
                </div>
            </article>      
        </div>
    </div>
</div>
<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>
