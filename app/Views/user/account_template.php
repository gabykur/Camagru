<?php ob_start(); ?>
<div class="background galleryB">
    <div id="test">
        <h2 id="title" style="padding-top:0;text-shadow: 4px 2px 1px #67e8a6;">Hey Kitty</h2>
        <div id="account">
            <nav id="account_nav">
                <a id="EdPro" href="/user/account">Edit Profile</a>
                <a id="EdPwd" href="/user/modifyPassword">Edit Password</a>
                <a id="DelPho" href="/user/deletePhotos">Delete Photos</a>
                <a id="DelAcc" href="/user/deleteAccount">Delete Account</a>
                <a id="Notif" href="/user/notifications">Notifications</a>
            </nav>
            <article>
                <div style="max-height: 705px;" id="a">
                    <div class="loginForm accountForm">
                        <h2 id="subTitle"><?php echo $pageTitle; ?></h2>
                        <form action="<?php echo $formAction; ?>" method="post">
                            <span style="color:green; margin-top: 34px;"><?php echo $message; ?></span>
                            <span style="color:red; margin-top: 34px;"><?php echo $error; ?></span>
                            <?php echo $formContent; ?>
                        </form>
                    </div><br>
                </div>
            </article>
        </div>
    </div>
</div>

<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>
