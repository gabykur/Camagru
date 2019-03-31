<?php 
require("config/database.php");

$PhotoPerPage = 9;
$query = $pdo->query('SELECT id_img FROM picture');
$AllPhotos = $query->rowCount();
$AllPages = ceil($AllPhotos/$PhotoPerPage);
if(isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] <= $AllPages) {
   $_GET['page'] = intval($_GET['page']);
   $page = $_GET['page'];
} else {
   $page = 1;
}
$start = ($page-1) * $PhotoPerPage;

//counting number of likes and comments

/*$countLikes = $pdo->prepare("SELECT count(likes.id_img) AS likes FROM likes WHERE id_img = :id_img");
$countLikes->bindParam(':id_img', $id_photo);
$countLikes->execute();
$likes = $countLikes->fetch(PDO::FETCH_ASSOC);

$countComments = $pdo->prepare("SELECT count(comments.id_img) AS comments FROM  WHERE id_img = :id_img");
$countComments->bindParam(':id_img', $id_photo);
$countComments->execute();
$comments = $countComments->fetch(PDO::FETCH_ASSOC);*/

?>

<?php ob_start();?>
<div class="background galleryB">
    <h2 id="title" style="letter-spacing:10px">Gallery</h2>
    <div id="photoDisplay" >
        <?php
            $stmt = $pdo->query("SELECT picture.id_img, picture.img, picture.date, users.username, count(comments.id_img) AS nb_comment, count(likes.id_img) AS nb_like
                                FROM picture
                                LEFT JOIN comments ON (picture.id_img = comments.id_img) 
                                LEFT JOIN likes ON (picture.id_img = likes.id_img)
                                INNER JOIN users ON picture.id_user = users.id 
                                GROUP BY picture.id_img 
                                ORDER BY picture.date DESC 
                                LIMIT $start, $PhotoPerPage");
            foreach ($stmt as $photos){
        ?>
        <div id='img'>
            <?php
                session_start();
                if (empty($_SESSION['loggedin'])){
                    echo '
                        <a href="http://'.$_SERVER['HTTP_HOST'].'/user/login.php"><img src='.$photos['img'].' id='.$photos['id_img'].'></a>';
                } else{
                    echo '
                        <a href="http://'.$_SERVER['HTTP_HOST'].'/user/addLikeCom.php?id='.$photos['id_img'].'"><img src='.$photos['img'].' id='.$photos['id_img'].'></i></a>';
                }
            ?>
            <div id='buttons'>
            <?php
                session_start();
                if (empty($_SESSION['loggedin'])){
                    echo '
                        <a href="http://'.$_SERVER['HTTP_HOST'].'/user/login.php"><i class="fas fa-heart"></i>  '.$photos['nb_like'].'</a>
                        <a href="http://'.$_SERVER['HTTP_HOST'].'/user/login.php"><i class="fas fa-comment" ></i>  '.$photos['nb_comment'].'</a>';
                } else{
                    echo '
                        <a href="http://'.$_SERVER['HTTP_HOST'].'/user/addLikeCom.php?id='.$photos['id_img'].'"><i class="fas fa-heart"></i>  '.$photos['nb_like'].'</a>
                        <a href="http://'.$_SERVER['HTTP_HOST'].'/user/addLikeCom.php?id='.$photos['id_img'].'"><i class="fas fa-comment" ></i>  '.$photos['nb_comment'].'</a>';
                }
            ?>
            </div>
        </div>
        <?php
            }
         ?>
    </div>
</div>

 <div class="pagination">
    <?php
        if ($page > 1){
            echo '<a href="index.php?page='.($page-1).'" id="number">&#8249;</a> ';
        }
        for($i=1;$i<=$AllPages;$i++) {
            echo '<a href="index.php?page='.$i.'" id="number">'.$i.'</a> ';
        }
        if ($page < $AllPages){
            echo '<a href="index.php?page='.($page+1).'" id="number">&#8250;</a> ';
        }
    ?>
    </div>
<?php $view = ob_get_clean(); ?>
<?php require("template.php"); ?>