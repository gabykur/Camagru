<?php ob_start(); ?>
<div class="loginForm">
    <h2 id="title2">Reset password</h2>
    <p style="color:green;"><?php echo $message; ?></p>
    <p style="color:red;"><?php echo $err_invalid; ?></p>
    <form action="/auth/resetPassword" method="post">
        <input type="password" name="password" placeholder="New Password" value="<?php echo htmlspecialchars($password); ?>">
        <span><?php echo $password_err; ?></span>
        <input type="password" name="confirm_password" placeholder="Confirm New Password" value="<?php echo htmlspecialchars($confirm_password); ?>">
        <span><?php echo $confirm_password_err; ?></span>
        <input type="hidden" name="reset" value="<?php if(isset($_GET['reset'])){ echo htmlspecialchars($_GET['reset']); } ?>">
        <input type="submit" value="Reset Password" name="reset_password">
    </form>
</div><br/>    
<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>
