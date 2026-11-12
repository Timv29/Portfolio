<?php
    require_once 'php/login.php';
?>
<header>
    <div class="logoName">
        <img class="logo" src="images/LinoPosts.png" width="100px" height="100px">
        <h1 id="sitename"><a href="index" target="_self">LinoPosts</a></h1>
    </div>
    <?php 
        if(!isset($_SESSION['login'])){
    ?>
    <div class="login">
        <form id="loginForm" method="post" action="<?= isset($_GET['postId']) ? 'post/'.$_GET['postId'] : 'index' ?>">
            <div class="loginInputs">
                <div class="formfield">
                    <input id="username" name="username" type="text" placeholder="Username" autocomplete="off">
                    <p class="error"><?= $loginErrors['username'] ?? '' ?></p>
                </div>
                <div class="formfield">
                    <input id="password" name="password" type="password" placeholder="Password" autocomplete="off">
                    <p class="error"><?= $loginErrors['password'] ?? '' ?></p>
                </div>
            </div>
                <button type="submit" name="login" id="login">Log In</button>
        </form>
        <p class="error"><?= $loginErrors['login'] ?? '' ?></p>
        <p class="regLink">No account? <a href="registerForm">Register here</a></p>
    </div>
    <?php
        }
        else{
            $user = unserialize($_SESSION['login']);
    ?>

    <div class="logout">
        <p>Welcome back <?= $user->getUsername() ?>!</p>
        <form id="logoutForm" method="post" action="<?= isset($_GET['postId']) ? 'post/'.$_GET['postId'] : 'index'?>">
            <button type="submit" name="logout" id="logout">Log Out</button>
        </form>
    </div>

    <?php
        }
    ?>
</header>