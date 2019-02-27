<?php
require("../config/database.php");

function test_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$password_err = $confirm_password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST['resetPasswordForm'])){
        if(empty(test_input($_POST['password']))){
            $password_err = "Please enter a password.";     
        } elseif(strlen(test_input($_POST["password"])) < 6){
            $password_err = "Password must have atleast 6 characters.";
        } else{
            $password = test_input($_POST["password"]);
        }
        if(empty(test_input($_POST["confirm_password"]))){
            $confirm_password_err = "Please confirm password.";     
        } else{
            $confirm_password = test_input($_POST["confirm_password"]);
            if(empty($password_err) && ($password != $confirm_password)){
                $confirm_password_err = "Password did not match.";
            }
        }
        $query = $pdo->prepare('SELECT token FROM users WHERE token = :token');
        $query->bindParam(':token', $_POST['reset']);
        $query->execute();
        //var_dump($query->execute());
        $tokenExists = $query->fetch(PDO::FETCH_ASSOC);
        //var_dump($tokenExists);
        if ($tokenExists['token']){
         //   echo "as cia";
            if(empty($password_err) && empty($confirm_password_err)){
                $new_password = password_hash($password, PASSWORD_DEFAULT);
                $update_pass = "UPDATE users SET password = :password WHERE token = :token";
                $stmt = $pdo->prepare($update_pass);
                $stmt->execute(array(
                    ':password' => $new_password,
                    ':token' => $_POST['reset']
                ));
                $message = "Your password has been changed. You'll be sooon redirected to login page";
                header("Refresh: 5; url=login.php");
            }else{
                $message_err = "Your password wasn't changed.";
            }
        }else{
            echo "token does not exist";
        }
    }
}
?>

<?php ob_start();?>
<h2 style="text-align:center;margin-bottom: 35px;">Reset password</h2>
<div class="loginForm">
    <p style="color:green;"><?php echo $message; ?></p>
    <p style="color:red;"><?php echo $message_err; ?></p>
        <form action="" method="post">
            <div  class="<?php echo (!empty($password_err)) ? 'logError' : ''; ?>">
                <input type="password" name="password" placeholder="New Password" value="<?php echo $password; ?>">
                <span><?php echo $password_err; ?></span>
            </div>
            <div  class="<?php echo (!empty($confirm_password_err)) ? 'logError' : ''; ?>">
                <input type="password" name="confirm_password" placeholder="Confirm New Password" value="<?php echo $confirm_password; ?>">
                <span><?php echo $confirm_password_err; ?></span>
            </div>
            <input type="hidden" name="reset" value="<?php if(isset($_GET['reset'])){echo($_GET['reset']);}?>">
            <input type="submit" value="Reset Password" name="resetPasswordForm">
        </form>
</div><br/>    
<?php $view=ob_get_clean();?>
<?php require("../index.php");?>