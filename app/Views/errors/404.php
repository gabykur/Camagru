<?php ob_start(); ?>
<div class="background">
    <h1>404 Not Found</h1>
    <p>The page you are looking for does not exist.</p>
    <a href="/index.php">Go to Home</a>
</div>
<?php $view = ob_get_clean(); ?>
<?php require '../template.php'; ?>