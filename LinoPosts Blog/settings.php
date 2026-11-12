<?php
    require_once "vendor/autoload.php";
    use classes\DatabaseHandler;
    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    if(!isset($_SESSION)){
        session_start();
    }

    if(isset($_SESSION['login'])){
        $user = unserialize($_SESSION['login']);
    }
    else{
        header("Location: index");
    }

    if(isset($_SESSION['errors'])){
        $errors = $_SESSION['errors'];
        unset($_SESSION['errors']);
    }

    if(isset($_SESSION['success'])){
        $success = $_SESSION['success'];
        unset($_SESSION['success']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once "includes/head.php"?>
<body>
    <?php include_once "includes/header.php"?>
    <?php include_once "includes/sidebar.php"?>
    <div class="main settings">
        <form id="settingsForm" method="post" action="php/saveChanges.php">
            <p class="success"><?= $success ?? '' ?></p>
            <h2>Notification Settings</h2>
            <div class="setting">
                <p>Get notified when a user comments on you post:</p>
                <label class="switch">
                    <input type="checkbox" name="commentNotification" <?= $user->getCommentNotifEnabled() ? 'checked' : '' ?>>
                    <span class="slider"></span>
                </label>       
            </div>
            <div class="setting">
                <p>Get notified when a user replies to your comment:</p>
                <label class="switch">
                    <input type="checkbox" name="replyNotification" <?= $user->getReplyNotifEnabled() ? 'checked' : '' ?>>
                    <span class="slider"></span>
                </label>
            </div>
            <button type="submit" name="save" id="save">Save Changes</button>
        </form>
        <?php
            if(isset($errors)){
                foreach($errors as $error){
                    echo '<p class="error">'.$error.'</p>';
                }
            }
        ?>
    </div>
</body>