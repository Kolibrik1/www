<?php
@session_start();
include "profileNav.php";
require_once("./functions/functions.php");
$db = connect();
?>

<div id="allies">

    <?php
    loadRequests();
    loadAllies();

    if (isset($_GET["allyRequest"])) {
        if ($_GET["allyRequest"] == 1) {
            sendRequest();
            header("Location: ?page=allies");
        } else if ($_GET["allyRequest"] == 0) {
            denyRequest();
            header("Location: ?page=allies");
        } else if ($_GET["allyRequest"] == 3) {
            breakAlliance();
            header("Location: ?page=allies");
        }

    }
    ?>

</div>