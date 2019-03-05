<?php ob_start(); ?>
<div id="background">
    <div id="edProfile">
        <h2>Account</h2>
        <a href="user/modifyProfile.php">Edit Profile</a>
        <a href="user/modifyPassw.php">Modify Password</a>
        <a href="user/ChangeEmail.php">Change Email</a>
    </div>
</div>
<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>