<?php
    require_once "../vendor/autoload.php";
    use ConsoleTVs\Profanity\Classes\Blocker;
    use classes\DatabaseHandler;
    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable(__DIR__."/../");
    $dotenv->load();

    if(!isset($_SESSION)){
        session_start();
    }

    $errors = [];
    if(isset($_POST['edit'])){
        $block = new Blocker('', "***", ['en', 'nl']);
        $block->dictionary("../JS/filter.json");
        if(isset($_SESSION['postId'])){
            $postId = $_SESSION['postId'];
            unset($_SESSION['postId']);
        }
        else{
            header("Location: ../post");
            exit;
        }

        if(isset($_SESSION['login'])){
            $user = unserialize($_SESSION['login']); 
        }
        else{
            header("Location: ../post/".$postId ?? '0');
            exit;
        }

        if(!empty($_POST['title'])){
            if(strlen($_POST['title']) <= 255){
                $block->text($_POST['title']);
                if($block->clean()){
                    $title = ($_POST['title']);
                }
                else{
                    $errors['title'] = "Title contains words that are not allowed on LinoPosts";
                }
            }
            else{
                $errors['title'] = "Title cannot exceed length of 255 characters";
            }
        }
        else{
            $errors['title'] = "No title submitted";
        }

        if(strlen(trim($_POST['text']))){
            if(strlen($_POST['text']) <= 20000){
                $block->text($_POST['text']);
                if($block->clean()){
                    $text = preg_replace("/(\n|\r|<p><\/p>|<\/br>){5,}/",'$1$1$1$1$1',$_POST['text']);
                }
                else{
                    $bad = $block->badWords();
                    $errors['text'] =  'Post contains words that are not allowed on LinoPosts';
                }               
            }
            else{
                $errors['text'] = 'Post is too long';
            }
        }
        else{
            $errors['text'] = "Empty post submitted";
        }
    }
    else{
        header("Location: ../index");
        exit;
    }

    if(count($errors) === 0){
        $dbh = new DatabaseHandler($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);
        try{
            if($dbh->editPost($postId, $title, $text)){
                header("Location: ../post/".$postId ?? '0');
                exit;
            }
            else{
                $_SESSION['errors']['database'] = "Error while editing post";
                header("Location: ../myPosts");
                exit;
            }
        }
        catch(Exception $e){
            $_SESSION['errors']['database'] = "Error while editing post: ".$e->getMessage();
            header("Location: ../myPosts");
            exit;
        }
    }
    else{
        $_SESSION['errors'] = $errors;
        header("Location: ../myPosts");
        exit;    
    }
?>