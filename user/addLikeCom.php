<?php
require("../config/database.php");

if(isset($_GET['id'])){
    $query = $pdo->prepare("SELECT picture.id_img, picture.img, picture.date, users.username 
                FROM picture INNER JOIN users ON picture.id_user = users.id WHERE picture.id_img = ?");
    $query->execute(array($_GET['id']));
    $photo = $query->fetchAll(PDO::FETCH_ASSOC);
}
echo date_format($photo[0]['date'], 'd/m/y');

?>


<?php ob_start();?>
<div class="background galleryB">
    <div id="photoDisplay">
        <div id="imgLikeCom">
            <img src="../<?= $photo[0]['img'] ?>">
            <p id="img_info"><?= $photo[0]['username'] ?> </br> <?= date('j M Y', strtotime($photo[0]['date']));?></p>
        </div>
        <div id="box">

            <form action="" class="comment_form" method="post">
		        <textarea class="textbox" rows="3" maxlength="250" name="message" placeholder="Add a comment..." required></textarea>
		        <input type="submit" id="sendBtt" value="Send">
		    </form>   
        </div>   
    </div>
    
</div>

<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>
