<?php require("config/database.php");

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

?>

<?php ob_start();?>
<div class="background galleryB">
    <h2 id="title">Gallery</h2>
    <div id="photoDisplay" >
        <?php
            $stmt = $pdo->query("SELECT * FROM picture ORDER BY date DESC LIMIT $start, $PhotoPerPage");
            foreach ($stmt as $photos){
                echo "
                <div id='img'>
                    <img src='".$photos['img']."' id='".$photos['id_img']."'>
                </div>";
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