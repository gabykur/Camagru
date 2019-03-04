<?php ob_start(); ?>
    <div id="edProfile">
        <h1>Account</h1>
        <a href="user/modifyProfile.php">Edit Profile</a>
        <a href="user/modifyPassw.php">Modify Password</a>
        <a href="user/ChangeEmail.php">Change Email</a>
    </div>
<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>