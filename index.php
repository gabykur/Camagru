<?php require("config/database.php");

$PhotoPerPage = 4;
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
<div id="background">
    <h2 style="text-align:center;margin-bottom: 35px;">Gallery</h2>
    <div id="photoDisplay" >
        <?php
            $stmt = $pdo->query("SELECT * FROM picture ORDER BY date DESC LIMIT $start, $PhotoPerPage");
            foreach ($stmt as $photos){
                echo "<img src='".$photos['img']."' id='img' id='".$photos['id_img']."'>";
            }
         ?>
    </div>
</div>

 <div class="pagination">
    <?php
    if ($page > 1){
        echo '<a href="index.php?page='.($page-1).'">Previous</a> ';
    }
    for($i=1;$i<=$AllPages;$i++) {
        echo '<a href="index.php?page='.$i.'" class="pagination_item">'.$i.'</a> ';
    }
    if ($page < $AllPages){
        echo '<a href="index.php?page='.($page+1).'">Next</a> ';
    }
    ?>
    </div>
<?php $view = ob_get_clean(); ?>
<?php require("template.php"); ?>