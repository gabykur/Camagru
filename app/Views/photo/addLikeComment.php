<?php ob_start(); ?>
<div class="background galleryB">
    <div id="likeComPhoto">
        <div id="imgLikeCom">
            <img src="/<?= htmlspecialchars($photo['img'], ENT_QUOTES, 'UTF-8') ?>">
            <p id="img_info"><?= htmlspecialchars($photo['username'], ENT_QUOTES, 'UTF-8') ?> </br> <?= date('j M Y', strtotime($photo['date'])); ?></p>
            <?php 
                if ($likeStatus) {
                    echo '<a href="?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES, 'UTF-8') . '&dislike=' . $photo['id_img'] . '" class="likeIcon">' . $likes['likes'] . '  <i class="fas fa-heart"></i></a>';
                } else {
                    echo '<a href="?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES, 'UTF-8') . '&like=' . $photo['id_img'] . '" class="likeIcon">' . $likes['likes'] . '  <i class="far fa-heart"></i></a>';
                }
            ?>
        </div>
        <div id="box">
            <div class="commentForm">
                <div class="comments-container">
                    <?php foreach ($all_comments as $data): ?>
                        <div class='comment-wrapper'>
                            <p class='comtxt'><b id='usertxt'><?= htmlspecialchars($data['username'], ENT_QUOTES, 'UTF-8') ?></b> <?= htmlspecialchars($data['comment'], ENT_QUOTES, 'UTF-8') ?></p>
                            <?php if ($data['id_user'] == $_SESSION['id']): ?>
                                <form action="" method="post" class="delete-form">
                                    <input type="hidden" name="comment_id" value="<?= $data['id_comment'] ?>">
                                    <button type="submit" name="delete_comment" class="delete-btn"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <form action="" class="comment_form" method="post" id="commentForm">
                    <textarea class="textbox" rows="3" maxlength="250" name="comment" placeholder="Add a comment..." required></textarea>
                    <input type="submit" id="sendBtt" value="Post">
                </form>   
            </div>
        </div>   
    </div>
</div>
<script src="/public/js/comment.js"></script>
<?php $view = ob_get_clean(); ?>
<?php require('../template.php'); ?>
