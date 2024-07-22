<?php ob_start(); ?>
<div style="max-height: 705px;" id="a">
    <div class="loginForm" style="min-height:364px;">
        <form action="/auth/login" method="post">
            <h2 id="title2">Login</h2>
            <?php if (!empty($activation_message)): ?>
                <div class="message" style="color: green;"><?php echo $activation_message; ?></div>
            <?php endif; ?>
            <span><?php echo $username_err; ?></span>
            <input type="text" name="username" placeholder="Username" value="<?php echo $username; ?>">
            <span><?php echo $password_err; ?></span>
            <input type="password" name="password" placeholder="Password">
            <input type="submit" value="Login">
            <p>Forgot your password? <a href="/auth/forgotPassword">Click here!</a></p>
        </form>
    </div><br>
    <div class="loginForm">
        <p style="text-align:center">Don't have an account? <a href="/auth/register">Sign up now</a></p>
    </div><br>
</div>
<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>
