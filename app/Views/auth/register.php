<?php ob_start(); ?>
<div class="loginForm" style="min-height:364px;">
    <h2 id="title2">Sign Up</h2>
    <p id="actMsg" style="color:green;"><?php echo $activation_mess; ?></p><br>
    <form action="/auth/register" method="post">
        <span><?php echo $username_err; ?></span>
        <input type="text" name="username" placeholder="Username" value="<?php echo $username; ?>">
        <span><?php echo $email_err; ?></span>
        <input type="email" name="email" placeholder="Email" value="<?php echo $email; ?>">
        <span><?php echo $password_err; ?></span>
        <input type="password" name="password" placeholder="Enter Password" value="<?php echo $password; ?>">
        <span><?php echo $confirm_password_err; ?></span>
        <input type="password" name="confirm_password" placeholder="Confirm Password" value="<?php echo $confirm_password; ?>">
        <input type="submit" value="Register">
    </form>
</div><br>
<div class="loginForm">
    <p>Already have an account?<a href="/auth/login"> Login here</a></p>
</div><br>
<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>
