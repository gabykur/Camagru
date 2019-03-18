<?php ob_start(); ?>
<div class="background galleryB">
    <div id="test">
    <h2 id="title" style="padding-top:0;text-shadow: 4px 2px 1px #67e8a6;">What's up bitch ? </h2>
        <div id="account">
            <nav id="account_nav">
                <a href="account.php">Edit Profile</a>
                <a href="modifyPassw.php">Edit Password</a>
                <a href="deletePhotos.php" >Delete Photos</a>
                <a href="deleteAccount.php" >Delete Account</a>
                <a href="notifications.php" >Notifications</a>
            </nav>
            <article>
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
            </article>
        </div>
    </div>
</div>

<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>