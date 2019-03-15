<?php ob_start(); ?>

            <h2 style="margin-top: 20px;">Delete your Photos</h2>
                <div style="max-height: 705px;" id="a">
                    <div class="loginForm" style="min-height:364px; margin-top: 30px;">      
                        <form action="" method="post">
                            
                            <p id="actMsg"><?php echo $activation_mess; ?></p><br>
                            <span><?php echo $username_err; ?></span>
                            <input type="text" name="username" placeholder="New Username" value="<?php echo $username; ?>">
                            <input type="submit" name="change_username" value="Save">
                            <span><?php echo $password_err; ?></span>
                            <input type="password" name="password" placeholder="New Password">
                            <input type="password" name="confirm_password" placeholder="Confirm New Password">
                            <input type="submit" name="change_password" value="Save">
                        </form>
                    </div><br>
                </div>
<?php $content = ob_get_clean(); ?>
<?php require("account.php"); ?>