<?php
require("config/database.php");
date_default_timezone_set('Europe/Paris');

if (isset($_POST['tookAphoto']))
{
    $photo = $_POST['tookAphoto'];
    $photo = explode(',', $photo);
    $data = base64_decode($photo[1]);
    $filePath = 'public/upload/'.date("YmdHis").'.png';
    file_put_contents($filePath, $data);

}

session_start();

if(empty($_SESSION['loggedin']))
    header('Location: index.php');

?> 
<?php ob_start(); ?>

<div id="camera">
    <div id="video_div">
        <div id="live_video">
            <img src="public/stickers/poop.png" id="overlay">
        </div>
        <video id="video"></video>
    </div>
    <div id="sticker_div">
        <img src="public/stickers/poop.png" class="stickerImg active">
        <img src="public/stickers/peach.png" class="stickerImg">
        <img src="public/stickers/watermelon.png" class="stickerImg">
        <img src="public/stickers/pig.png" class="stickerImg">
        <img src="public/stickers/callme.png" class="stickerImg">
    </div>
    
    <form method="POST" action="" onsubmit=takePhoto();>
        <button id="snap" type="submit"  name="tookAphoto" value=""></button>
    </form>
    
    <canvas style="display:none" id="canvas" width=560 height=420></canvas>
    <div id='camera_gallery'>
        <img >
    </div>
</div>
<script src="/public/js/camera.js"></script>
<?php $view = ob_get_clean(); ?>
<?php require("index.php"); ?>