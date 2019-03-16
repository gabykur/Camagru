<?php ob_start(); ?>
    <div style="max-height: 705px;" id="a">
        <div class="loginForm accountForm" style="min-height:220px;margin-top:77px;">      
            <h2 id="subTitle">Delete Your Account</h2>
            <form action="" method="post">
                <p id="actMsg"><?php echo $activation_mess; ?></p><br>
                <span><?php echo $password_err; ?></span>
                <input type="passwrod" style="margin-top:41px;" name="password" placeholder="Enter password to delete account" value="<?php echo $passwrod; ?>">
                <input type="submit" id="saveBtt" style="width: 37%;margin-top: 15px;font-size: 22px;" name="delete_account" value="Delete Account">
            </form>
        </div><br>
    </div>
<?php $content = ob_get_clean(); ?>
<?php require("account.php"); ?>