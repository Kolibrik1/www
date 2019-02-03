<?php

@session_start();
require_once ("./functions/functions.php");
$db=connect();
?>
<header>

    <a href ="index.php"> <img id="header-logo" src="./images/InstantGramLogo.png"></a>
    <a id="header-web-title" href="index.php">InstantGram</a>


    <nav id="nav">


    <form class="searchBar" action="" method="get">
        <input  type="text" placeholder="Search user..." name="search">
        <button type="submit" >Search</button>
        <?php
        findUser();
        ?>

    </form>


        <a href="<?= "?page=register"?>">Register</a>
        <a href="<?= "?page=login"?>">Login</a>


    </nav>



</header>