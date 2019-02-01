<?php
    include "profileNav.php"
?>
<div id="accountInfo">
    <form method="post"   action="index.php">
        <div class="container">

            <label for="uname"><b>Username</b></label>
            <input type="text" placeholder="Username" name="uname" >

            <label for="mail"><b>E-mail</b></label>
            <input type="email" placeholder="E-mail" name="mail" >

            <label for="psw"><b>Password</b></label>
            <input type="password" placeholder="Password" name="psw" >

            <button type="submit" >Change</button>

        </div>




    </form>
</div>