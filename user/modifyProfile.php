<?php ob_start(); ?>
    <div style="max-height: 705px;" id="a">
        <div class="loginForm accountForm" style="min-height:364px; margin-top: 30px;">      
        <h2 id="subTitle">Edit your profile</h2>
            <form action="" method="post">
                <span><?php echo $username_err; ?></span>
                <input type="text" style="border: 3px solid #efb4e4;margin:14px;" name="username" placeholder="New Username" value="<?php echo $username; ?>">
                <input type="email" style="border: 3px solid #6cf1ac;margin:14px;" name="email" placeholder="New Email" value="<?php echo $email; ?>">
                <span><?php echo $password_err; ?></span>
                <input type="password" style="border: 3px solid #4bb7ec;margin:14px;" name="password" placeholder="New Password" value="<?php echo $password; ?>">
                <input type="password" style="border: 3px solid #4bb7ec;margin:14px;" name="confirm_password" placeholder="Confirm New Password" value="<?php echo $confirm_password; ?>">
                <input type="submit" id="saveBtt" value="Save">
            </form>
        </div><br>
    </div>
<?php $content = ob_get_clean(); ?>
<?php require("account.php"); ?>