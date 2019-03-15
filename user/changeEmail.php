<?php ob_start(); ?>

<h2 style="margin-top: 20px;">Change your email</h2>
<div style="max-height: 705px;" id="a">
    <div class="loginForm" style="min-height: 275px; margin-top: 30px;">      
        <form action="" method="post">
            <p id="actMsg"><?php echo $activation_mess; ?></p><br>
            <span><?php echo $email_err; ?></span>
            <input type="email" style="margin-top:57px;" name="email" placeholder="New Email Adress" value="<?php echo $email; ?>">
            <input type="submit" style="width: 37%;margin-top: 35px;" name="change_password" value="Change Email">
        </form>
    </div><br>
</div>
<?php $content = ob_get_clean(); ?>
<?php require("account.php"); ?>