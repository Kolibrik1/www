<div id="profileNav">

    <div id="profileName">
        <?php
        @session_start();

        $name = $_SESSION["login"];
        echo "<a > $name</a>";
        ?>
    </div>

    <a href="<?= "?page=publicGallery" ?>">Public Gallery</a>
    <a href="<?= "?page=privateGallery" ?>">Private Gallery</a>
    <a href="<?= "?page=accinfo" ?>">Account information</a>
    <a href="<?= "?page=allies" ?>">Allies</a>

    <?php
    if (isset($_SESSION["login"]) && $_SESSION["isAdmin"]) {

        $path = "index.php?page=manageUsers";

        echo "   <a href='$path'>Manage users</a>";


    }
    ?>

</div>

