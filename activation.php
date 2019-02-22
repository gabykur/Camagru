<?php
require("config/database.php");

$message = '';

if(isset($_GET['activationCode'])){
    $query = "SELECT * FROM users WHERE activation_code = :activation_code";
    $statement = $pdo->prepare($query);
    $statement->execute(array(':activation_code'   => $_GET['activationCode']));
    $no_of_row = $statement->rowCount();

    if($no_of_row > 0)
    {
        $result = $statement->fetchAll();
        //var_dump($result);
        foreach($result as $row){
            if($row['user_status'] == 'not verified'){
                //echo"as esu ciiiia";
                $update_query = "UPDATE users SET user_status = 'verified' WHERE username = :username";
                $statement = $pdo->prepare($update_query);
                $sub_result = $statement->fetchAll();
                $statement->execute(array(':username' => $_GET['username']));
                var_dump($statement->execute(array(':username' => $_GET['username'])));
                var_dump($sub_result);
                if(isset($sub_result)){
                    $message = '<label class="text-success">Your Email Address Successfully Verified <br />You can login here - <a href="login.php">Login</a></label>';
                }
            }else{
                $message = '<label class="text-info">Your Email Address Already Verified</label>';
                header("Refresh: 2; url=login.php");
            }
        }
    }else{
        $message = '<label class="text-danger">Invalid Link</label>';
    }
}

?>
<!DOCTYPE html>
<html>
 <head>
  <title>PHP Register Login Script with Email Verification</title>  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
 </head>
 <body>
  
  <div class="container">
   <h1 align="center">PHP Register Login Script with Email Verification</h1>
  
   <h3><?php echo $message; ?></h3>
   
  </div>
 
 </body>
 
</html>