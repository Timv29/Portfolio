<?php
    if(!isset($_SESSION)){
        session_start();
    }
    if(isset($_GET['error'])){
        if(isset($_SESSION['registerErrors'])){
            $regErrors = $_SESSION['registerErrors'];
            $data = $_SESSION['registerData'];

            unset($_SESSION['registerErrors']);
            unset($_SESSION['registerData']);
        }
        else{
            header("Location: registerForm.php");
            exit;
        }
    }
    if(isset($_GET['succes'])){
        if(isset($_SESSION['succes'])){
            $succes = $_SESSION['succes'];

            unset($_SESSION['succes']);
        }
        else{
            header("Location: registerForm.php");
            exit;            
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once "includes/head.php"?>
<body>
    <?php include_once "includes/header.php" ?>
    <?php include_once "includes/sidebar.php" ?>
    <div class="main register">
        <a href="index"><img id="backArrow" src="images/back.svg" width="40" height="40"></a>
        <form id="registerForm" method="post" action="php/register.php">
            <?= isset($regErrors['pdo']) ? '<p class="error">'.$regErrors['pdo'].'<p>' : '' ?>
            <?= isset($succes) ? '<p class="succes">'.$succes.'<p>' : '' ?>
            <div class="formfield">
                <div>
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" value="<?= $data['username'] ?? '' ?>">
                </div>
                <?= isset($regErrors['username']) ? '<p class="error">'.$regErrors['username'].'<p>' : '' ?>
            </div>
            <div class="formfield">
                <div>
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?= $data['email'] ?? '' ?>">
                </div>
                <?= isset($regErrors['email']) ? '<p class="error">'.$regErrors['email'].'<p>' : '' ?>
            </div>
            <div class="formfield">
                <div>
                    <label for="pass">Password</label>
                    <input type="password" name="pass" id="pass" value="<?= $data['pass'] ?? '' ?>">
                </div>
                <?= isset($regErrors['pass']) ? '<p class="error">'.$regErrors['pass'].'<p>' : '' ?>
            </div>
            <div class="formfield">
                <div>
                    <label for="confirmPass">Confirm password</label>
                    <input type="password" name="confirmPass" id="confirmPass">
                </div>
                <?= isset($regErrors['confirmPass']) ? '<p class="error">'.$regErrors['confirmPass'].'<p>' : '' ?>
            </div>
            <button type="submit" name="register">Register</button>
        </form>
    </div>
</body>
</html>