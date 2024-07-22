<?php ob_start(); ?>
<div class="background">
    <div id="camera">
        <div id="video_div">
            <div id="live_video">
                <img src="/public/stickers/poop.png" id="overlay">
            </div>
            <video id="video"></video>
        </div>
        <div id="sticker_div">
            <?php foreach ($stickers as $sticker): ?>
                <img src="<?= $sticker['path'] ?>" class="stickerImg" data-id="<?= $sticker['id_sticker'] ?>" onclick="selectSticker(this)">
            <?php endforeach; ?>
        </div>
        <form method="POST" action="" onsubmit="takePhoto();">
            <input id="photo" name="photo" type="hidden" value="">
            <input id="sticker" name="sticker" type="hidden" value="">
            <input id="snap" style="display:none;" type="submit" name="tookAphoto" value="">
        </form>
        <canvas style="display:none" id="canvas" width="640" height="480"></canvas>
        <div id='camera_gallery'>
            <?php foreach ($user_photos as $photo): ?>
                <img src="<?= $photo['img'] ?>" id='photo' id='<?= $photo['id_img'] ?>'>
            <?php endforeach; ?>
        </div>
        <div><p id="text-camera">No camera? No problem! <a style="color: #EFB4E4;" href="/photo/upload"><b>Click here :)</b></a></p></div>
        <canvas style="display:none" id="canvasCopy" width="640" height="480"></canvas>
    </div>
</div><br/><br/>
<script src="/public/js/camera.js"></script>
<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>
