<?php ob_start(); ?>
    <div id="edProfile">
        <h2>Account</h2>
        <a href="user/modifyProfile.php">Edit Profile</a>
        <a href="user/modifyPassw.php">Modify Password</a>
        <a href="user/changeEmail.php">Change Email</a>
        <a href="user/deleteAccount.php">Delet Account</a>
    </div>
<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>