<?php
include "profileNav.php";
@session_start();
require_once("./functions/functions.php");

$uname = $_SESSION["login"];
?>


<form method="post" id="uploadJson" action="" enctype="multipart/form-data">
    <input type="file" name="fileToUpload" accept="application/json">
    <input type="submit" name="submitJson" value="Upload Json">

    <?php importUsers();?>
</form>

<div id="manageUsers">

    <?php
    loadUsers();

    if (isset($_GET["manageRequest"])) {
        echo "fafaaffafa";
        if ($_GET["manageRequest"] == 1) {
            deleteUser($_GET["id"]);
            header("Location: ?page=manageUsers");
        }

    }

    ?>


</div>
