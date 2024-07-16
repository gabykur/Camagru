<?php 
require("config/database.php");

session_start();

function getPhotoCount($pdo) {
    $query = $pdo->query('SELECT id_img FROM picture');
    return $query->rowCount();
}

function getPhotos($pdo, $start, $photos_per_page) {
    $stmt = $pdo->prepare("SELECT picture.id_img, picture.img, picture.date, picture.likes, users.username, COUNT(comments.id_img) AS nb_comment
                           FROM picture
                           LEFT JOIN comments ON (picture.id_img = comments.id_img) 
                           INNER JOIN users ON picture.id_user = users.id 
                           GROUP BY picture.id_img 
                           ORDER BY picture.date DESC 
                           LIMIT :start, :photos_per_page");
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':photos_per_page', $photos_per_page, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllPages($all_photos, $photos_per_page) {
    return ceil($all_photos / $photos_per_page);
}

$photos_per_page = 9;
$all_photos = getPhotoCount($pdo);
$all_pages = getAllPages($all_photos, $photos_per_page);
$page = (isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] <= $all_pages) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $photos_per_page;
$photos = getPhotos($pdo, $start, $photos_per_page);
?>

<?php ob_start(); ?>
<?php if (!empty($all_photos)): ?>
<div class="background galleryB">
    <h2 id="title" style="letter-spacing:10px">Gallery</h2>
    <div id="photoDisplay">
        <?php foreach ($photos as $photo): ?>
        <div id='img'>
            <?php if (empty($_SESSION['loggedin'])): ?>
                <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/user/login.php"><img src="<?php echo $photo['img']; ?>" id="<?php echo $photo['id_img']; ?>"></a>
            <?php else: ?>
                <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/user/addLikeCom.php?id=<?php echo $photo['id_img']; ?>"><img src="<?php echo $photo['img']; ?>" id="<?php echo $photo['id_img']; ?>"></a>
            <?php endif; ?>
            <div id='buttons'>
                <?php if (empty($_SESSION['loggedin'])): ?>
                    <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/user/login.php"><i class="fas fa-heart"></i> <?php echo $photo['likes']; ?></a>
                    <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/user/login.php"><i class="fas fa-comment"></i> <?php echo $photo['nb_comment']; ?></a>
                <?php else: ?>
                    <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/user/addLikeCom.php?id=<?php echo $photo['id_img']; ?>"><i class="fas fa-heart"></i> <?php echo $photo['likes']; ?></a>
                    <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/user/addLikeCom.php?id=<?php echo $photo['id_img']; ?>"><i class="fas fa-comment"></i> <?php echo $photo['nb_comment']; ?></a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="pagination">
    <?php
    if ($page > 1) {
        echo '<a href="index.php?page='.($page-1).'" class="page-link">&#8249;</a> ';
    }
    for ($i = 1; $i <= $all_pages; $i++) {
        if ($i === $page) {
            echo '<a href="index.php?page='.$i.'" class="page-link active-page">'.$i.'</a> ';
        } else {
            echo '<a href="index.php?page='.$i.'" class="page-link">'.$i.'</a> ';
        }
    }
    if ($page < $all_pages) {
        echo '<a href="index.php?page='.($page+1).'" class="page-link">&#8250;</a> ';
    }
    ?>
</div>
<?php endif; ?>

<?php 
$view = ob_get_clean();
require("template.php");
?>
