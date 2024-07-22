<?php ob_start(); ?>
<div style="min-height:250px;">
    <div class="loginForm" style="border:none;background-color:transparent">
        <h2 id="title2">Activate your account</h2>
        <h3 style="text-align:center"><?php echo $message; ?></h3>
        <input type="button" value="Login" onclick="window.location='/auth/login'" />
    </div>
</div>
<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>
