<?php
session_start();

if(empty($_SESSION['loggedin']))
    header('Location: index.php');
?> 
<?php ob_start(); ?>

<div id="camera">
    <div id="video_div">
        <div id="overlay">
            <img src="public/stickers/poop.png">
        </div>
        <video id="video"></video>
        <img id='camera_img'/>
    </div>
    <div id="sticker_div">
        <img src="public/stickers/poop.png" >
    </div>
    <button id="snap"></button>
    <div id='camera_gallery'>
        <canvas id="canvas"></canvas>
        
    </div>
</div>
<script src="/public/js/camera.js"></script>
<?php $view = ob_get_clean(); ?>
<?php require("index.php"); ?>