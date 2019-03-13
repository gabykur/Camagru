<?php ob_start(); ?>
<div class="background galleryB">
<h2>Account</h2>
    <div id="edProfile">
       
        <a href="user/modifyProfile.php">Edit Profile</a>
        <a href="user/modifyPassw.php">Modify Password</a>
        <a href="user/changeEmail.php">Change Email</a>
        <a href="user/deleteAccount.php">Delet Account</a>
    </div>
    <br/><br/><br/>
</div>
<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>