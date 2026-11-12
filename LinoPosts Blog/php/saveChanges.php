<?php
    require_once "../vendor/autoload.php";

    use classes\DatabaseHandler;
    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable(__DIR__."/../");
    $dotenv->load();

    if(!isset($_SESSION)){
        session_start();
    }

    if(isset($_SESSION['login'])){
        $user = unserialize($_SESSION['login']); 
    }
    else{
        header("Location: ../index.php");
        exit;
    }

    if(isset($_POST['save'])){
        if(isset($_POST['commentNotification'])){
            $comment = true;
        }
        else{
            $comment = false;
        }

        if(isset($_POST['replyNotification'])){
            $reply = true;
        }
        else{
            $reply = false;
        }
    }
    else{
        header("Location: ../settings.php");
        exit;
    }


    try{
        $dbh = new DatabaseHandler($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);
        if($dbh->updateSettings($user->getId(), $comment, $reply)){
            $_SESSION['login'] = serialize($dbh->getUser($user->getUsername()));
            $_SESSION['success'] = 'Settings saved successfully!';
        }
        else{
            $_SESSION['errors']['settings'] = 'Something went wrong, settings could not be saved.';
        }
        header("Location: ../settings.php");
        exit;
    }
    catch(Exception $e){
        $_SESSION['errors']['database'] = $e->getMessage();
        header("Location: ../settings.php");
        exit;
    }
?>