<?php 
require("../config/database.php");
session_start();

if (isset($_POST['delete_photo'])){
    $checked = $_POST['delete_img'];
    //var_dump($checked);
    foreach($checked as $id){
        var_dump($id);
        $sql = "DELETE  FROM picture  WHERE picture.id_img = '".$id."'";
	    $pdo->query($sql);
    }
    //header("Location:deletePhotos.php");
}
?>

<?php ob_start(); ?>
<div class="background galleryB">
    <div id="test">
    <h2 id="title" style="padding-top:0;text-shadow: 4px 2px 1px #67e8a6;">What's up bitch ? </h2>
        <div id="account">
            <nav id="account_nav">
                <a href="account.php">Edit Profile</a>
                <a href="modifyPassw.php">Edit Password</a>
                <a href="deletePhotos.php" >Delete Photos</a>
                <a href="deleteAccount.php" >Delete Account</a>
                <a href="notifications.php" >Notifications</a>
            </nav>
            <article>
                <div style="max-height: 705px;" id="a">
                    <div class="loginForm accountForm" style="min-height:364px; margin-top: 13px;width: 91%;">      
                        <h2 id="subTitle">Delete Photos</h2>
                            <div id="deletePhotos" >
                            <?php  
                                $stmt = $pdo->prepare("SELECT img, id_img FROM picture WHERE id_user = :id_user ORDER BY date DESC");
                                $stmt->bindParam(":id_user", $_SESSION['id']);
    	            	        $stmt->execute();
                                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($res as $photos){
                                    var_dump($photos);
                                    echo "
                                    <div id='img'>
                                        <img src='../".$photos['img']."' id='".$photos['id_img']."'>
                                        <input type='checkbox' id='check_del' name='delete_img[]' value='".$photos['id_img']."'>
                                    </div>";
                                }
                            ?>
                             </div>
                             <div class="loginForm accountForm" style="background:none; box-shadow:none">
                                <form method="POST" action="">
                                     <input type="submit" id="saveBtt" style="width: 22%;margin-top: 11px;font-size: 24px;margin-bottom:7px" name="delete_photo" value="Delete">          
                                </form>
                            </div>
                    </div><br>
                </div>
            </article>
        
        </div>
    </div>
</div>

<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>