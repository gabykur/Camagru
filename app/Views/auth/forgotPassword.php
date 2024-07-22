<?php ob_start(); ?>
<div class="loginForm">
    <h2 id="title2">Forgotten Password</h2>
    <p id="actMsg" style="color:green;"><?php echo $reset_mess; ?></p>
    <form method="post" action="/auth/forgotPassword" style="margin-top:7%;">
        <input type="text" placeholder="Enter your login" name="username">
        <input type="email" placeholder="Enter your email" name="email">
        <span><?php echo $error; ?></span><br />
        <input type="submit" value="Send Link" name="forgot_password">
    </form>
</div><br/>
<div class="loginForm">
    <p style="text-align:center">Know your password? <a href="/auth/login"> Login</a></p>
</div><br>
<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>
