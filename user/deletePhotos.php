<?php 
require("../config/database.php");
session_start();
?>


<?php ob_start(); ?>
<div style="max-height: 705px;" id="a">
        <div class="loginForm accountForm" style="min-height:364px; margin-top: 30px;">      
        <h2 id="subTitle">Delete Photos</h2>
        <div id="photoDisplay" >
        <?php
            //require("..config/database.php");
            //var_dump($_SESSION['id']);
            //$stmt = $pdo->prepare("SELECT img, id_img FROM picture WHERE id_user = :id_user ORDER BY date DESC");
            //var_dump($stmt);
            //$stmt->bindParam(":id_user", $_SESSION['id']);
    		//$stmt->execute();
            //var_dump($stmt->execute());
            //$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $res = $pdo->query("SELECT * FROM picture");
            var_dump($res);
            foreach ($res as $photos){
                echo "
                <div id='img'>
                    <img src='".$photos['img']."' id='".$photos['id_img']."'>
                </div>";
            }
        ?>
        </div>
        </div><br>
    </div>
<?php $content = ob_get_clean(); ?>
<?php require("account.php"); ?>