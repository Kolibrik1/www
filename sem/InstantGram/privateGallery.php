<?php
@session_start();
include "profileNav.php";
require_once ("./functions/functions.php");
$db=connect();
?>


<form method="post" id="uploadPicture" action="" enctype="multipart/form-data">
    <input type="file" name="fileToUpload" accept="image/*">
    <input type="submit" name="submit" value="Upload Image">

    <?php uploadFile(0);?>
</form>


<div id="gallery">

    <?php loadGallery(0);?>

</div>