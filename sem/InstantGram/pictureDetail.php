<?php
@session_start();
include "profileNav.php";
require_once ("./functions/functions.php");
$db=connect();
?>



<div id="pictureDetail">

    <?php
    loadImage($_GET["id"],$_GET["fileType"]);
    ?>
    <form method="post" id="deleteImage" action="">


        <button type="submit" name="deleteIm">Delete Image</button>
        <button type="submit" name="toJson">Comments to JSON.</button>
      <?php
      if(isset($_POST["deleteIm"])) {

          deleteImage($_GET["id"]);
          header("Location: index.php?page=publicGallery");
      }

      if(isset($_POST["toJson"])){

          commentsToJson($_GET["id"]);

          $id=$_GET["id"];
          $ft=$_GET["fileType"];
          $path= "index.php?page=pictureDetail&id=$id&fileType=$ft";
          header("Location: $path");
      }
      ?>
    </form>

    <form id="likeCounter" action="">
        <button disabled type="button" name="unlike"> - </button>
        <?php
        loadLikes($_GET["id"]);
        ?>
        <button disabled type="button" name="like"> + </button>

    </form>

</div>

<div id="description">

    <form method="post" action="">
        <textarea name="description" cols="40" rows="5" placeholder="Write description...">
             <?php
             loadDescription($_GET["id"],1);
             ?>
        </textarea>
        <button type="submit" name="submitDescription">Submit</button>
        <?php  postDescription($_GET["id"]); ?>
    </form>

</div>



<div id="writeComment" >
    <form method="post" action="">
        <textarea required name="comment" cols="40" rows="5" placeholder="Write comment..."></textarea>
        <button type="submit" name="submitComment">Submit</button>
        <?php  postComment($_GET["id"]); ?>
    </form>
</div>


<div id="comments">
    <?php
    loadComments($_GET["id"]);
    ?>

</div>

