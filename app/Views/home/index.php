<?php ob_start(); ?>
<?php if (!empty($all_photos)): ?>
<div class="background galleryB">
    <h2 id="title" style="letter-spacing:10px">Gallery</h2>
    <?php if ($message): ?>
        <div class="message" style="color:green; text-align:center;"><?php echo $message; ?></div>
    <?php endif; ?>
    <div id="photoDisplay">
        <?php foreach ($photos as $photo): ?>
        <div id='img'>
            <?php if (empty($_SESSION['loggedin'])): ?>
                <a href="/auth/login"><img src="<?php echo $photo['img']; ?>" id="<?php echo $photo['id_img']; ?>"></a>
            <?php else: ?>
                <a href="/photo/addLikeComment?id=<?php echo $photo['id_img']; ?>"><img src="<?php echo $photo['img']; ?>" id="<?php echo $photo['id_img']; ?>"></a>
            <?php endif; ?>
            <div id='buttons'>
                <?php if (empty($_SESSION['loggedin'])): ?>
                    <a href="/auth/login"><i class="fas fa-heart"></i> <?php echo $photo['likes']; ?></a>
                    <a href="/auth/login"><i class="fas fa-comment"></i> <?php echo $photo['nb_comment']; ?></a>
                <?php else: ?>
                    <a href="/photo/addLikeComment?id=<?php echo $photo['id_img']; ?>"><i class="fas fa-heart"></i> <?php echo $photo['likes']; ?></a>
                    <a href="/photo/addLikeComment?id=<?php echo $photo['id_img']; ?>"><i class="fas fa-comment"></i> <?php echo $photo['nb_comment']; ?></a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="pagination">
    <?php
    if ($page > 1) {
        echo '<a href="/home/index?page='.($page-1).'" class="page-link">&#8249;</a> ';
    }
    for ($i = 1; $i <= $all_pages; $i++) {
        if ($i === $page) {
            echo '<a href="/home/index?page='.$i.'" class="page-link active-page">'.$i.'</a> ';
        } else {
            echo '<a href="/home/index?page='.$i.'" class="page-link">'.$i.'</a> ';
        }
    }
    if ($page < $all_pages) {
        echo '<a href="/home/index?page='.($page+1).'" class="page-link">&#8250;</a> ';
    }
    ?>
</div>
<?php endif; ?>

<?php 
$view = ob_get_clean();
require __DIR__ . "/../template.php";
