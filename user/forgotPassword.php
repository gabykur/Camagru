<?php
require("../config/database.php");

function test_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$email_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST['forgotPassword'])){
        if(empty(test_input($_POST['email']))){
            $email_err = "Please enter an email";
        }else{
            $email = test_input($_POST["email"]);
        }  
        $query = $pdo->prepare('SELECT email FROM users WHERE email = :email');
        $query->bindParam(':email', $email);
        $query->execute();
        $userExists = $query->fetch(PDO::FETCH_ASSOC);
        var_dump($userExists);
        //$pdo = null;
        if ($userExists['email']){
            //echo "as cia";
            $token = bin2hex(openssl_random_pseudo_bytes(16));
            $updated_token = "UPDATE users SET token = :token WHERE email = :email";
            $stmt = $pdo->prepare($updated_token);
            //var_dump($stmt);
            $stmt->execute(array(
                ':token' => $token,
                ':email' => $email
            ));
            /*var_dump( $stmt->execute(array(
                ':token' => $token,
                ':email' => $email
            )));*/
            $to      = $email; // Send email to our user
            $subject = 'Reset password'; // Give the email a subject 
            $message = '

            Dear user,
      
            If this e-mail does not apply to you please ignore it. 
            It appears that you have requested a password reset. 

            To reset your password, please click the link below :
            http://localhost:8082/user/resetPassword.php?reset='.$token.'

            If you cannot click it, please paste it into your web browser\'s address bar.

            Thanks,
            The Administration :)';

            $headers = 'From:noreply@gabriele.com' . "\r\n"; // Set from headers
            mail($to, $subject, $message, $headers); // Send our email
            //header("location: login.php");
            $reset_mess = "Email has been sent to $email";
            unset($stmt);
            //var_dump($stmt);
        }else{
            $email_err = "No user with that e-mail address exists.";
        }   
    }unset($pdo);
}
?>
        


<?php ob_start();?>
    <h2 style="text-align:center;margin-bottom: 35px;">Forgotten Password</h2>
    <div class="loginForm">
        <p id="actMsg" style="color:green;"><?php echo $reset_mess; ?></p>
        <form method="post" action="" style="margin-top:7%;">
          <input type="email" placeholder="Enter your email" name="email">
          <span><?php echo (!empty($email_err)); ?></span><br />
          <input type="submit" value="Send Link" name="forgotPassword">
        </form>
    </div><br/>
    <div class="loginForm">
            <p style="text-align:center">Know your password ? <a href="login.php"> Login</a></p>
    </div><br>
<?php $view = ob_get_clean();?>
<?php require("../index.php");?>