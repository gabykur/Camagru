<?php session_start();?>
<?php ob_start(); ?>
<div class="background galleryB">
    <div id="test">
    <h2 id="title" style="padding-top:0;text-shadow: 4px 2px 1px #67e8a6;">What's up <?php /*echo $_SESSION['username'];*/?> bitch ? </h2>
        <div id="account">
            <nav id="account_nav">
                <a href="modifyProfile.php">Edit Profile</a>
                <a href="notifications.php" >Notifications</a>
                <a href="deletePhotos.php" >Delete Photos</a>
                <a href="deleteAccount.php" >Delete Account</a>
            </nav>
            <article>
                
                <?= $content ?>
            </article>
        
        </div>
    </div>
</div>

<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>