<?php ob_start(); ?>
<div style="max-height: 705px;" id="a">
        <div class="loginForm accountForm" style="min-height:364px; margin-top: 30px;">      
        <h2 id="subTitle">Notifications</h2>
            <form action="" method="post">
                <label><input id="checkbox" type="checkbox" /><p id="pNot">Enable notifications on comments</p></label>
            </form>
        </div><br>
    </div>
<?php $content = ob_get_clean(); ?>
<?php require("account.php"); ?>