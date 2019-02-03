<div id="profileNav">

    <div id="profileName">
        <?php
        @session_start();
        $id = $_GET["id"];
        $name = findName($id);
        echo "<a > $name</a>";
        ?>
    </div>

    <a href="<?= "?page=otherPublicGallery&id=$id" ?>">Public Gallery</a>
    <?php

    if ((isset($_SESSION["login"]) && isAlly($_SESSION["id"], $id)) || (isset($_SESSION["isAdmin"]))&&$_SESSION["isAdmin"]) {


        echo "   <a href='index.php?page=otherPrivateGallery&id=$id'>Private Gallery</a>";

    }

    if (isset($_SESSION["login"]) && !pendingRequest() && !isAlly($_SESSION["id"], $id)) {
        $path = $_SERVER["REQUEST_URI"] . "&allyRequest=1";

        echo "   <a href='$path'>Ally</a>";
        sendRequest();
    }


    ?>


</div>


