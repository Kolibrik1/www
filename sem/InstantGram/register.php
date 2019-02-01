<?php

@session_start();
require_once ("./functions/functions.php");
$db=connect();

?>

<div id="mujRegister">
    <form  action="" method="post">
        <div class="container">

            <label for="uname"><b>Username</b></label>
            <input type="text" placeholder="Enter Username" name="uname" required>




            <label for="psw"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="psw" required>

            <label for="repsw"><b>Re-enter Password</b></label>
            <input type="password" placeholder="Re-enter Password" name="repsw" required>

            <button type="submit" name="btnSubmitRegistered">Register</button>

        </div>

        <div class="container" style="background-color:#f1f1f1">
            <button type="button" onclick="window.location='http://localhost/school/InstantGram/index.php';return false;">Cancel</button>

        </div>

        <?php
        register();
        ?>



    </form>
</div>