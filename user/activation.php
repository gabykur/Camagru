<?php
require("../config/database.php");

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
                    $message = '<span style="color:green">Your Email Address Successfully Verified</span>';
                }
            }else{
                $message = '<span style="color:blue">Your Email Address Already Verified</span>';
            }
        }
    }else{
        $message = '<span style="color:darkred">Invalid Link</span>';
    }
}
?>

<?php ob_start();?>
<div style="min-height:250px;">
<h2 style="text-align:center;margin-bottom: 35px;">Activate your account</h2>
   <h3 style="text-align:center"><?php echo $message; ?>!</h3>
   <div class="loginForm" style="border:none;background-color:transparent">
       <input type="submit" value="Login" 
       onclick="window.location='login.php'" />
    </div>
</div>
<?php $view=ob_get_clean();?>
 <?php require("../index.php");?>