<?php
session_start();
require("../config/database.php");

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$id_photo = $_GET['id'];

if(isset($_GET['id'])){
    $query = $pdo->prepare("SELECT picture.id_img, picture.img, picture.date, users.username 
                            FROM picture 
                            INNER JOIN users ON picture.id_user = users.id 
                            WHERE picture.id_img = ?");
    $query->execute(array($_GET['id']));
    $photo = $query->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_GET['like'])){
    $query = $pdo->prepare("INSERT INTO likes(id_user, id_img) VALUES(:id_user, :id_img)");
    $query->bindParam(':id_user', $_SESSION['id']);
    $query->bindParam(':id_img', $_GET['like']);
    $query->execute();
    header("Location: /user/addLikeCom.php?id=".$id_photo);
}

if (isset($_GET['dislike'])){
    $query = $pdo->prepare("DELETE FROM likes WHERE id_user = :id_user AND id_img = :id_img");
    $query->bindParam(':id_user', $_SESSION['id']);
    $query->bindParam(':id_img', $_GET['dislike']);
    $query->execute();
    header("Location: /user/addLikeCom.php?id=".$id_photo);
}

$countLikes = $pdo->prepare("SELECT count(likes.id_img) AS likes FROM likes WHERE id_img = :id_img");
$countLikes->bindParam(':id_img', $id_photo);
$countLikes->execute();
$likes = $countLikes->fetch(PDO::FETCH_ASSOC);

$comment = test_input($_POST['comment']);

if (!empty($comment)){
    $query = $pdo->prepare("INSERT INTO comments(id_img, id_user, comment) VALUES(:id_img, :id_user, :comment)");
    $query->execute(array(
        ':id_img' => $id_photo,
        ':id_user' => $_SESSION['id'],
        ':comment' => $comment

    ));
    header("Location: /user/addLikeCom.php?id=".$id_photo);

}



?>


<?php ob_start();?>
<div class="background galleryB">
    <div id="photoDisplay">
        <div id="imgLikeCom">
            <img src="../<?= $photo[0]['img'] ?>">
            <p id="img_info"><?= $photo[0]['username'] ?> </br> <?= date('j M Y', strtotime($photo[0]['date']));?></p>
            <?php 
                $query = $pdo->prepare("SELECT id_like  FROM likes WHERE id_img = :id_img AND id_user = :id_user");
                $query->bindParam(':id_img', $_GET['id']);
                $query->bindParam(':id_user', $_SESSION['id']);
                $query->execute();
                if ($query->fetchColumn()){
                    echo '<a href="?'.$_SERVER['QUERY_STRING'].'&dislike='.$photo[0]['id_img'].'" class="likeIcon">'.$likes['likes'].'  <i class="fas fa-heart" ></i></a>';
                }else{
                    echo '<a href="?'.$_SERVER['QUERY_STRING'].'&like='.$photo[0]['id_img'].'" class="likeIcon">'.$likes['likes'].'  <i class="far fa-heart" ></i></a>';
                }
            ?>
        </div>
        <div id="box">
            <div class="loginForm accountForm">
            <?php
                $allComments = $pdo->query("SELECT comments.id_user, comments.comment, users.username
                                                FROM comments
                                                INNER JOIN users ON comments.id_user = users.id
                                                WHERE comments.id_img = $id_photo
                                                ORDER BY comments.date ASC");
                foreach($allComments as $data){
                    echo "<p>".$data['comment']."</p>";
                }
            ?>
            <form action="" class="comment_form" method="post">
                <textarea class="textbox" rows="3" maxlength="250" name="comment" placeholder="Add a comment..." required></textarea>
		        <input type="submit" id="sendBtt" value="Post">
		    </form>   
            </div>
        </div>   
    </div>
    
</div>

<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>
