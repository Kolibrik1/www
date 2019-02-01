<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="./images/InstantGramLogo.png">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>InstantGram | We're having a party tonight!</title>
</head>




<body>



<?php


$isLoggedIn=false;


if(isset($isLoggedIn)){
    if($isLoggedIn){
        include "header_2.php";
    }else {
        include "header_1.php";


    }

}else {
    $isLoggedIn=false;
    include "header_1.php";

}
?>



<?php



if(isset($_GET["page"])) {
    if($_GET["page"]=="login"||$_GET["page"]=="register"){
        include "hero.php";
    }
    if($_GET["page"]=="logout"){
        $isLoggedIn=false;
        echo'<meta http-equiv="refresh" content="0;url=index.php">';


    }else{
    $file = "./" . $_GET["page"] . ".php";
    }
}else {
    include "hero.php";
}
if(isset($file)&&file_exists($file)){
    include $file;
}
?>



<?php

include "footer.php";
?>



</body>


</html>