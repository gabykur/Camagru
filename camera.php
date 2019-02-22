<?php
require("/config/database.php");

function get_stickers()
{
    $sql = "SELECT * FROM sticker";
    $data = $pdo->query($sql);
    return ($data);
}

$sticker = get_stickers();

require("home.php");

?>