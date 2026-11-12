<?php
    require_once "../vendor/autoload.php";
    use classes\DatabaseHandler;
    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable(__DIR__."/../");
    $dotenv->load();

    if(!isset($_SESSION)){
        session_start();
    }

    if(isset($_POST['delete'])){
        if(isset($_SESSION['postId'])){
            $postId = $_SESSION['postId'];
            unset($_SESSION['postId']);
        }
        else{
            header("Location: ../index");
            exit;
        }
    }
    else{
        header("Location: ../index");
        exit;
    }
    
    try{
        $dbh = new DatabaseHandler($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);

        if($dbh->deletePost($postId)){
            $_SESSION['deleteSucces'] = "Post deleted succesfully";
            header("Location: ../myPosts");
            exit;
        }
        else{
            $_SESSION['error'] = "Error while deleting post";
            header("Location: ../myPosts");
            exit;
        }
    }
    catch(Exception $e){
        $_SESSION['error'] = "Error while deleting post: ".$e->getMessage();
        header("Location: ../myPosts");
        exit;
    }
?>