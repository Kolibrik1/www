
<header>

    <a href ="index.php"> <img id="header-logo" src="./images/InstantGramLogo.png"></a>
    <a id="header-web-title" href="index.php">InstantGram</a>


    <nav id="nav">

        <form class="searchBar" action="" method="get">
            <input  type="text" placeholder="Search user..." name="search">
            <button type="submit" >Search</button>
        </form>

        <a href="<?= "?page=logout"?>">Logout</a>
        <a href="<?= "?page=publicGallery"?>">Profile</a>

    </nav>
</header>