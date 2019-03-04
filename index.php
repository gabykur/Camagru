<?php require("config/database.php");?>
<?php ob_start();?>
<h2 style="text-align:center;margin-bottom: 35px;">Gallery</h2>
<div id="photoDisplay" >
    <?php
        $stmt = $pdo->prepare("SELECT * FROM picture ORDER BY date DESC ");
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($res);
        foreach ($res as $photos){
            echo "<img src='".$photos['img']."' id='img' id='".$photos['id_img']."'>";
        }
    ?>
</div>
<?php $view = ob_get_clean(); ?>
<?php require("template.php"); ?>