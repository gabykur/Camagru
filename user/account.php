<?php ob_start(); ?>
<div class="background galleryB">
<div id="test">
<h2 id="title"style="padding-top:0">Account</h2>
    <div id="account">
        <nav>
            <a href="user/modifyProfile.php">Edit Profile</a>
            <a href="user/modifyPassw.php">Modify Password</a>
            <a href="user/changeEmail.php">Change Email</a>
            <a href="user/deleteAccount.php">Delete Account</a>
        </nav>
        <article>

        </article>
        
    <div>
</div>
</div>
<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>