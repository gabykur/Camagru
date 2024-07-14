<?php
session_start();
require("../config/database.php");

if (empty($_SESSION['loggedin'])) {
    header('Location: ../index.php');
    exit;
}

$username = $_SESSION['id'];
$error = "";
$password = "";

function test_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_account'])) {
    if (isset($_POST['password']) && !empty($_POST['password'])) { //Checks if the password is written
        $password = test_input($_POST['password']);

        $sql = "SELECT id, password FROM users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $_SESSION['id']);
        $stmt->execute();
        $hashed_pwd = $stmt->fetch();

        if($hashed_pwd && password_verify($password, $hashed_pwd['password'])){ //Checks if the password is correct
            $query = $pdo->prepare("SELECT * FROM picture WHERE id_user = :id_user");
            if ($query->execute(array(':id_user' => $_SESSION['id']))){ // Deletes the photos from the file 'upload'
                $filesToDelete = $query->fetchAll(PDO::FETCH_ASSOC);
                foreach ($filesToDelete as $fileToDelete) {
                    unlink("../" . $fileToDelete['img']);    
                }
            }

            //Deletes the user + all the rest
            $pdo->query("DELETE FROM comments WHERE comments.id_user = $username");
            $query = $pdo->prepare("SELECT id_img FROM likes WHERE id_user = ?");
            $query->execute(array($username));
            $res = $query->fetchAll(PDO::FETCH_ASSOC);
            if ($res){
                foreach ($res as $likedPhoto){
                    $pdo->query("UPDATE picture SET likes = likes - 1 WHERE id_img = " . intval($likedPhoto['id_img']));
                }
            }
            $pdo->query("DELETE FROM likes WHERE likes.id_user = $username");
            $pdo->query("DELETE FROM picture WHERE picture.id_user = $username");

            $query = $pdo->prepare("DELETE FROM users WHERE id = :id"); 
            $query->bindParam(':id', $_SESSION['id']);
            //if the query is correct, the account is deleted and the the session is loggued out
            if ($query->execute()){
                $to      = $_SESSION['email'];
                $subject = 'Deletion Completed'; 
                $message = '

                    Dear '.$_SESSION['username'].',
            
                    Your account has been deleted.
                    See you never !
                '; 
                $headers = 'From:noreply@gabriele.com' . "\r\n"; 
                mail($to, $subject, $message, $headers);
                $message = "Account deleted";
                $_SESSION["loggedin"] = "";
                session_destroy();
                header("Location:../index.php");
                exit;
            }else{
                $error = "Your account wasn't deleted";
            }
        }else{
            $error = "Incorrect Password";
        }
    }else{
        $error = "Sorry mate, you failed";
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
                <div style="max-height: 705px;" id="a">
                    <div class="loginForm accountForm DelAcc">      
                        <h2 id="subTitle">Delete Your Account</h2>
                        <form action="" method="post">
                            <span><?php echo htmlspecialchars($error); ?></span>
                            <input type="password" style="margin-top:41px;" name="password" placeholder="Enter password to delete account" value="<?php echo htmlspecialchars($password); ?>" required>
                            <input type="submit" id="saveBtt" style="margin-top: 15px;font-size: 22px;" name="delete_account" value="Delete Account">
                        </form>
                    </div><br>
                </div>
            </article>
        </div>
    </div>
</div>

<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>