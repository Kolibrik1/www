<?php
include "profileNav.php";
@session_start();
require_once ("./functions/functions.php");

$uname= $_SESSION["login"];
?>

<div id="accountInfo">
    <form method="post"   action="">
        <div class="container">

            <label for="uname"><b>Username</b></label>
            <input type="text" placeholder=<?php echo "$uname"?> name="uname" >

            <label for="psw"><b>Password</b></label>
            <input type="password" placeholder="Password" name="psw" >

            <button type="submit" >Change</button>

        </div>

        <?php
        changeCredentials();
        ?>


    </form>
</div>
