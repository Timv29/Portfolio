<?php
    if(!isset($_SESSION)){ 
        session_start(); 
    } 
    
    if(!isset($_SESSION['lastVisit'])){
        $_SESSION['lastVisit'] = $_COOKIE['lastVisit'];
    }

    setcookie("lastVisit", date('d-m-Y H:i:s'), time()+60*60*24*30);

    //needs some finetuning but basic system works for cookie lastVisit
?>
<script src="./JS/nav.js" defer></script>
<nav class="sidebar">
    <div id="search">
        <form id="searchForm" method="get" action="results">
            <input type="text" name="search" placeholder="Search">
            <button type="submit"><img src="images/search.svg" width="15" height="15"></button>
        </form>
    </div>
<?php
    if(strpos($_SERVER['REQUEST_URI'], 'index')){
?>
    <div id="sort">
        <form id="sortForm" method="get" action="index">
            <?= isset($currentPage) ?
                '<input type="hidden" name="page" value="'.$currentPage.'">' : ''
            ?>
            <div class="formField">
                <label for="sort">Sort By: </label>
                <select name="sort" id="sort">
                    <option value="dateDesc" <?=isset($_GET['sort']) && $_GET['sort'] === 'dateDesc' ? 'selected' : '' ?>>Upload Date - Newest First</option>
                    <option value="dateAsc" <?=isset($_GET['sort']) && $_GET['sort'] === 'dateAsc' ? 'selected' : '' ?>>Upload Date - Oldest First</option>
                    <option value="authorAsc" <?=isset($_GET['sort']) && $_GET['sort'] === 'authorAsc' ? 'selected' : '' ?>>Author Name - A-Z</option>
                    <option value="authorDesc" <?=isset($_GET['sort']) && $_GET['sort'] === 'authorDesc' ? 'selected' : '' ?>>Author Name - Z-A</option>
                </select>
            </div>
            <div class="formField"> 
                <label for="limit">Posts per Page: </label>
                <select name="limit" id="limit">
                    <option <?=isset($_GET['limit']) && intval($_GET['limit']) === 5 ? 'selected' : '' ?>>5</option>
                    <option <?=isset($_GET['limit']) && intval($_GET['limit']) === 10 ? 'selected' : '' ?>>10</option>
                    <option <?=isset($_GET['limit']) && intval($_GET['limit']) === 15 ? 'selected' : '' ?>>15</option>
                    <option <?=isset($_GET['limit']) && $_GET['limit'] === 'none' ? 'selected' : '' ?> value="none">All</option>
                </select>
            </div>
            <button type="submit">Sort</button> 
        </form>
    </div>
<?php
    }
?>
    <div id="sidebarButtons">
        <div class="navbutton">
            <a href="index">Home</a>
        </div>
    <?php
        if(isset($_SESSION['login'])){
    ?>
        <div class="navbutton">
            <a href="postForm">Create Post</a>
        </div>
        <div class="navbutton">
            <a href="myPosts">My Posts</a>
        </div>
        <div class="navbutton">
            <a href="settings">Profile Settings</a>
        </div>
    <?php
        }
    ?>
    </div>
    <p id="lastVisit"><?= isset($_SESSION['lastVisit']) ? 'Last visit: '.$_SESSION['lastVisit'] : '' ?></p>
</nav>
    <div id="hideButton">
        <img src="images/down.svg" width="35" height="35">
    </div>