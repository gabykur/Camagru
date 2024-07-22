<?php ob_start(); ?>
<div class="background">
    <div id="camera">
        <div id="video_div">
            <div id="live_video">
                <img src="/public/stickers/poop.png" id="overlay">
            </div> 
            <img id='upload_img' width="100%" height="100%"/>
        </div>
        <div id="sticker_div">
            <?php foreach ($stickers as $sticker): ?>
                <img src="<?= $sticker['path']; ?>" class="stickerImg" data-id="<?= $sticker['id_sticker']; ?>" onclick="stickerSelector(this)">
            <?php endforeach; ?>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" id="upload" onsubmit="uploadPhoto()">
            <label for="uploadPic">Load file</label>
            <input type="file" name="uploadPic" id="uploadPic" style="display:none" accept="image/*">
            <input id="photo" name="photo" type="hidden" value="">
            <input id="sticker" name="sticker" type="hidden" value="1">
            <button type="submit" name="submit" id="uploadBtt" value="">Save</button>
        </form>

        <canvas style="display:none" id="canvas" width="640" height="480"></canvas>
    </div>
    <canvas style="display:none" id="canvasCopy" width="640" height="480"></canvas>
</div><br/><br/>
<script src="/public/js/upload.js"></script>
<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>
