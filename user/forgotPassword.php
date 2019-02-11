<?php
require("../config/database.php");

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

$email_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
  if(isset($_POST['forgotPassword'])){
    if (empty(test_input($_POST['email']))){
      $email_err = "Please enter an email";
    }else{
      $email = test_input($_POST["email"]);
    }  
    $query = $pdo->prepare('SELECT email FROM users WHERE email = :email');
    $query->bindParam(':email', $email);
    $query->execute();
    $userExists = $query->fetch(PDO::FETCH_ASSOC);
    $pdo = null;
    if ($userExists['email']){
      $token = bin2hex(openssl_random_pseudo_bytes(16));

      $to      = $email; // Send email to our user
      $subject = 'Signup | Verification'; // Give the email a subject 
      $message = '

      Thanks for signing up!
      Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.


      Please click this link to activate your account:
      http://localhost:8082/user/resetPassword.php?reset='.$token.'';

      $headers = 'From:noreply@gabriele.com' . "\r\n"; // Set from headers
      mail($to, $subject, $message, $headers); // Send our email
      //header("location: login.php");
      $reset_mess = "Email has been set to reset your paswword";
    }else{
      $email_err = "No user with that e-mail address exists.";
    }
  }
}
?>



<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
  <body>
    <div class="wrapper" style="align">
    <p style="color:red;"><?php echo $reset_mess; ?></p>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
          <p>Enter Email Address To Send Password Link</p>
          <input type="email" class="form-control" name="email">
          <span class="help-block"><?php echo $email_err; ?></span><br />
          <input type="submit" class="btn btn-primary" name="forgotPassword">
        </form>
    </div>
  </body>
</html>