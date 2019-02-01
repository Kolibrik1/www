<div id="profileNav">

<div id="profileName">
    <?php
    @session_start();

    $name=$_SESSION["login"];
    echo "<a > $name</a>";
    ?>
</div>

    <a href="<?= "?page=publicGallery"?>">Public Gallery</a>
    <a href="<?= "?page=privateGallery"?>">Private Gallery</a>
    <a href="<?= "?page=accinfo"?>">Account information</a>
    <a href="<?= "?page=allies"?>">Allies</a>

</div>

