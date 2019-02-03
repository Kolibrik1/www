<?php
@session_start();
include "profileNav2.php";
require_once ("./functions/functions.php");
$db=connect();
?>

<div id="pictureDetail">

    <?php
    loadOtherImage($_GET["picId"],$_GET["fileType"],$_GET["id"]);
    ?>
    <form id="likeCounter" action="" method="post">
        <button type="submit" name="unlike"> - </button>
        <?php
        loadLikes($_GET["picId"]);
        ?>
        <button type="submit" name="like"> + </button>
    <?php
    setApprove($_GET["picId"]); ?>
    </form>

</div>

<div id="description">
    <?php
    loadDescription($_GET["picId"],0);
    ?>

</div>

<?php
if(isset($_SESSION["login"])){
    $picId=$_GET["picId"];
echo "
<div id='writeComment' >
    <form method='post' action=''>
        <textarea required name='comment' cols='40' rows='5' placeholder='Write comment...'></textarea>
        <button type='submit' name='submitComment'>Submit</button>
        
    </form>
</div>
";
    postComment($picId);
}
?>
<div id="comments">
    <?php
    loadComments($_GET["picId"]);
    ?>

</div>

