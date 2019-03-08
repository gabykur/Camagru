<?php require("config/database.php");

$PhotoPerPage = 4;
$query = $pdo->query('SELECT id_img FROM picture');
$AllPhotos = $query->rowCount();
$AllPages = ceil($AllPhotos/$PhotoPerPage);
echo $AllPages;
if(isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] <= $AllPages) {
   $_GET['page'] = intval($_GET['page']);
   $currentPage = $_GET['page'];
} else {
   $currentPage = 1;
}
$start = ($currentPage-1) * $PhotoPerPage;

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
    <div id=photoDisplay>
    <?php
         for($i=1;$i<=$AllPages;$i++) {
            if($i == $currentPage) {
                echo $i.' ';
            } else {
                echo '<a href="index.php?page='.$i.'">'.$i.'</a> ';
             }
        }
    ?>
    </div>
</div>
<?php $view = ob_get_clean(); ?>
<?php require("template.php"); ?>