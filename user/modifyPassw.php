<?php
require("../config/database.php");

function test_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$old_password = "";
$new_password = "";
$confirm_password = "";

$error = "";

if(empty(test_input($_POST['old_password']))){
    $error = "Please enter your old password";     
} else{
    $old_password = test_input($_POST["old_password"]);
}

if (!empty($old_password)){
    
}

if(empty(test_input($_POST['new_password']))){
    $error = "Please enter a new password";     
} else{
    $new_password = test_input($_POST["new_password"]);
}
if(empty(test_input($_POST["new_confirm_password"]))){
    $error = "Please confirm password.";     
} else{
    $confirm_password = test_input($_POST["confirm_password"]);
    if(empty($error) && ($new_password != $confirm_password)){
        $confirm_password_err = "Password did not match.";
    }
}


?>


<?php ob_start(); ?>
<div class="background galleryB">
    <div id="test">
    <h2 id="title" style="padding-top:0;text-shadow: 4px 2px 1px #67e8a6;">What's up bitch ? </h2>
        <div id="account">
            <nav id="account_nav">
                <a href="account.php">Edit Profile</a>
                <a href="modifyPassw.php">Edit Password</a>
                <a href="deletePhotos.php" >Delete Photos</a>
                <a href="deleteAccount.php" >Delete Account</a>
                <a href="notifications.php" >Notifications</a>
            </nav>
            <article>
                <div style="max-height: 705px;" id="a">
                    <div class="loginForm accountForm" style="min-height:364px; margin-top: 30px;">      
                        <h2 id="subTitle">Edit your profile</h2>
                        <form action="" method="post">
                            <span style="color:red"><?php echo $message; ?></span>
                            <span style="color:red"><?php echo $error; ?></span>
                            <input type="password" style="border: 3px solid #6cf1ac;margin:14px;" name="old_password" placeholder="Old Password" value="">
                            <span></span>
                            <input type="password" style="border: 3px solid #4bb7ec;margin:14px;" name="new_password" placeholder="New Password" value="">
                            <input type="password" style="border: 3px solid #4bb7ec;margin:14px;" name="new_confirm_password" placeholder="Confirm New Password" value="">
                            <input type="submit" id="saveBtt" value="Update" name="save">
                        </form>
                    </div><br>
                </div>
            </article>
        
        </div>
    </div>
</div>
<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>