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
        if(isset($_SESSION['postId']) && isset($_SESSION['commentId'])){
            $postId = $_SESSION['postId'];
            unset($_SESSION['postId']);

            $commentId = $_SESSION['commentId'];
            unset($_SESSION['commentId']);

            $postAuthorId = $_SESSION['postAuthorId'];
            unset($_SESSION['postAuthorId']);
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


    if(isset($commentId) && isset($_SESSION['login'])){
        try{
            $dbh = new DatabaseHandler($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);
            $comment = $dbh->getCommentById($commentId);
            $authorId = $comment->getUserId();
            $userId = unserialize($_SESSION['login'])->getId();


            if($authorId === $userId){
                if(!$dbh->deleteComment($comment->getDatabaseId(), "Comment deleted by author")){
                    $_SESSION['errors']['delete'] = "Something went wrong, comment not deleted.";
                }
                header("Location: ../post/".$postId);
                exit;
            }
            elseif($postAuthorId === $userId){
                if($_POST['msg'] !== ''){
                    $msg = 'Comment removed by post author due to: '.htmlspecialchars($_POST['msg']);
                }
                else{
                    $msg = 'Comment removed by post author';
                }

                if(!$dbh->deleteComment($comment->getDatabaseId(), $msg)){
                    $_SESSION['errors']['delete'] = "Something went wrong, comment not deleted.";
                }
                header("Location: ../post/".$postId);
                exit;
            }
            else{
                header("Location: ../post/".$postId);
                exit;
            }
        }
        catch(Exception $e){
            $_SESSION['error']['database'] = $e->getMessage();
        }
    }
    else{
        header("Location: ../post/".$postId);
        exit;
    }
?>