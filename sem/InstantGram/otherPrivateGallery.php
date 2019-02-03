<?php
@session_start();
include "profileNav2.php";
require_once ("./functions/functions.php");
$db=connect();
?>



<div id="gallery">

    <?php loadOtherGallery(0,$_GET["id"]);?>

</div>