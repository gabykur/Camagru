<?php
require_once("../config/database.php");
session_start();
$username = $email = $password = $confirm_password = "";
$error = "";

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    //username modifications
    if(isset($_POST['save'])){
        if(empty(test_input($_POST["new_username"]))){
            $error = "Please enter a new username.";
        } else{
            $sql = "SELECT id FROM users WHERE username = :username";    
            if($stmt = $pdo->prepare($sql)){
                $stmt->bindParam(":username", test_input($_POST["new_username"]), PDO::PARAM_STR);
                if($stmt->execute()){
                    if($stmt->rowCount() == 1){
                        $error = "This username is already taken.";
                    } else{
                        $username = test_input($_POST["new_username"]);
                    }
                }
            }
            unset($stmt);
        }
        if(empty($error)){
            $update_user = "UPDATE users SET username = :username WHERE id = :id";
            $stmt = $pdo->prepare($update_user);
            $ok = $stmt->execute(array(
                ':username' => $username,
                ':id' => $_SESSION["id"]
            ));
            if ($ok){
                $message = "Updated :)";
                session_start();
                $_SESSION['username'] = $username;
                header("Refresh: 2; url=modifyProfile.php");
            }else{
                $message = "Something went wrong :(";
            }
            unset($stmt);
        }
    }
    //email modifications
    if(isset($_POST['save'])){
        if(empty(test_input($_POST["new_email"]))){
            $error = "Please enter a new email.";
        } else{
            $sql = "SELECT id FROM users WHERE email = :email";    
            if($stmt = $pdo->prepare($sql)){
                $stmt->bindParam(":email", test_input($_POST["new_email"]), PDO::PARAM_STR);
                if($stmt->execute()){
                    if($stmt->rowCount() == 1){
                        $error = "This email is already taken.";
                    } else{
                        $email = test_input($_POST["new_email"]);
                    }
                }
            }
            unset($stmt);
        }
        if(empty($error)){
            $update_email = "UPDATE users SET email = :email WHERE id = :id";
            $stmt = $pdo->prepare($update_email);
            $ok = $stmt->execute(array(
                ':email' => $email,
                ':id' => $_SESSION["id"]
            ));
            if ($ok){
                $message = "Updated :)";
                session_start();
                $_SESSION['email'] = $email;
                header("Refresh: 2; url=modifyProfile.php");
            }else{
                $message = "Something went wrong :(";
            }
            unset($stmt);
        }
    }
}

?>


<?php ob_start(); ?>
    <div style="max-height: 705px;" id="a">
        <div class="loginForm accountForm" style="min-height:364px; margin-top: 30px;">      
        <h2 id="subTitle">Edit your profile</h2>
            <form action="" method="post">
                <span style="color:red"><?php echo $message; ?></span>
                <span style="color:red"><?php echo $error; ?></span>
                <input type="text" style="border: 3px solid #efb4e4;margin:14px;" name="new_username" placeholder="New Username" value="<?php echo $_SESSION["username"]; ?>" require="">
                <input type="email" style="border: 3px solid #6cf1ac;margin:14px;" name="new_email" placeholder="New Email" value="<?php echo $_SESSION['email']; ?>">
                <span></span>
                <input type="password" style="border: 3px solid #4bb7ec;margin:14px;" name="new_password" placeholder="New Password" value="">
                <input type="password" style="border: 3px solid #4bb7ec;margin:14px;" name="new_confirm_password" placeholder="Confirm New Password" value="">
                <input type="submit" id="saveBtt" value="Update" name="save">
            </form>
        </div><br>
    </div>
<?php $content = ob_get_clean(); ?>
<?php require("account.php"); ?>