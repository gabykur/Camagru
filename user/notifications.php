<?php
session_start();
require("../config/database.php");

if(empty($_SESSION['loggedin']))
    header('Location: ../index.php');
?>

<?php ob_start(); ?>
<div class="background galleryB">
    <div id="test">
    <h2 id="title" style="padding-top:0;text-shadow: 4px 2px 1px #67e8a6;">What's up bitch ? </h2>
        <div id="account">
            <nav id="account_nav">
                <a id="EdPro" href="account.php">Edit Profile</a>
                <a id="EdPwd" href="modifyPassw.php">Edit Password</a>
                <a id="DelPho" href="deletePhotos.php" >Delete Photos</a>
                <a id="DelAcc" href="deleteAccount.php" >Delete Account</a>
                <a id="Notif" href="notifications.php" >Notifications</a>
            </nav>
            <article>
                <div style="max-height: 705px;" id="a">
                    <div class="loginForm accountForm" style="min-height:364px; margin-top: 30px;">      
                         <h2 id="subTitle">Notifications</h2>
                            <form action="" method="post">
                                <label id="notiflabel"><input id="checkbox" type="checkbox" /><p id="pNot">Enable notifications on comments</p></label>
                            </form>
                    </div><br>
                </div>
            </article>
        
        </div>
    </div>
</div>
<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>