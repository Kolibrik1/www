<?php

@session_start();

require_once ("./functions/functions.php");
$db=connect();



?>



<div id="mujLogin">
<form method="post"   action="">
    <div class="container">

        <label for="uname"><b>Username</b></label>
        <input type="text" placeholder="Enter Username" name="uname" required>

        <label for="psw"><b>Password</b></label>
        <input type="password" placeholder="Enter Password" name="psw" required>

        <button type="submit" >Login</button>

    </div>
        <div class="container" style="background-color:#f1f1f1">
            <button type="button" onclick="window.location='http://localhost/school/InstantGram/index.php';return false;">Cancel</button>


    </div>

    <?php
    login();
    ?>


</form>




</div>