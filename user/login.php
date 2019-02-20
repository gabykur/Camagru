<?php
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ../index.php");
    exit;
}
 
require_once("../config/database.php");
 
$username = $password = "";
$username_err = $password_err = "";
$activation_mess = "";

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
 
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    if(empty(test_input($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = test_input($_POST["username"]);
    }
    
    if(empty(test_input($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = test_input($_POST["password"]);
    }
    
    if(empty($username_err) && empty($password_err)){
        $sql = "SELECT * FROM users WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $username;
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetchAll()){
                        $id = $row[0]["id"];
                        $username = $row[0]["username"];
                        $hashed_password = $row[0]["password"];
                        if ($row[0]["user_status"] == 'verified'){
                            if(password_verify($password, $hashed_password)){
                                session_start();
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;                            
                                header("location: ../home.php");
                            } else{
                                $password_err = "The password you entered was not valid.";
                            }
                        }else{
                            $activation_mess = "The account is not yet verified. Please go check your email.";
                        }
                    }
                } else{
                    $username_err = "No account found with that username.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        unset($stmt);
    }
    unset($pdo);
}
?>
<?php require("../index.php"); ?>
<div class="main">
    <div class="loginForm">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>
        <p style="color:red;"><?php echo $activation_mess; ?></p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
            <p>Forgot your password ?<a href="forgotPassword.php">Click here!</a>.</p>
            <p>Go back home ? <a href="../index.php">Return</a>.</p>
        </form>
    </div>
</div>