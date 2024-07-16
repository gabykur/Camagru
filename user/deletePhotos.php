<?php 
require("../config/database.php");
session_start();

if (empty($_SESSION['loggedin'])) {
    header('Location: ../index.php');
    exit;
}

$message = "";
$message_err = "";

if(isset($_POST['delete'])){
    if (isset($_POST['check']) && !empty($_POST['check'])) {
        $checkbox = $_POST['check'];
        $del_id = implode(",", array_map('intval', $checkbox));

        // Fetch the images to delete
        $query = $pdo->prepare("SELECT img FROM pictures WHERE id_img IN ($del_id)");
        $query->execute();
        $filesToDelete = $query->fetchAll(PDO::FETCH_ASSOC);

        // Delete the images from the filesystem
        foreach ($filesToDelete as $file) {
            unlink("../" . $file['img']);    
        }

        // Use prepared statements to delete from database
        $tables = ['comments', 'likes', 'pictures'];
        foreach ($tables as $table) {
            $stmt = $pdo->prepare("DELETE FROM $table WHERE id_img IN ($del_id)");
            $stmt->execute();
        }
        
        $message = "Selected photos have been deleted successfully.";
        header("Refresh: 2; url=deletePhotos.php");
        exit;
    }else{
        $message_err = "Please choose photos to delete";
    }
}
?>

<?php ob_start(); ?>
<div class="background galleryB">
    <div id="test">
    <h2 id="title" style="padding-top:0;text-shadow: 4px 2px 1px #67e8a6;">Hey Kitty </h2>
        <div id="account">
            <nav id="account_nav">
                <a id="EdPro" href="account.php">Edit Profile</a>
                <a id="EdPwd" href="modifyPassw.php">Edit Password</a>
                <a id="DelPho" href="deletePhotos.php" >Delete Photos</a>
                <a id="DelAcc" href="deleteAccount.php" >Delete Account</a>
                <a id="Notif" href="notifications.php" >Notifications</a>
            </nav>
            <article>
                <div class="loginForm accountForm DelPho">      
                    <h2 id="subTitle">Delete Photos</h2>
                    <span style="color:green"><?php echo htmlspecialchars($message); ?></span>
                    <span style="color:red"><?php echo htmlspecialchars($message_err); ?></span>
                    <div id="deletePhotos">      
                        <form method="POST" action="">
                            <?php 
                                $stmt = $pdo->prepare("SELECT img, id_img FROM pictures WHERE id_user = :id_user ORDER BY date DESC");
                                $stmt->bindParam(":id_user", $_SESSION['id']);
                                $stmt->execute();
                                $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($photos as $photo) {
                                    echo "
                                    <div id='img'>
                                        <img src='../" . htmlspecialchars($photo['img']) . "'>
                                        <input type='checkbox' id='check_del' name='check[]' value='" . intval($photo['id_img']) . "'>
                                    </div>";
                                }
                            ?>
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