<?php
    require_once "../vendor/autoload.php";
    use ConsoleTVs\Profanity\Classes\Blocker;
    use classes\DatabaseHandler;
    use classes\Post;
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
        header("Location: ../postForm.php");
        exit;
    }

    $dbh = new DatabaseHandler($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);
    $errors = [];

    if(isset($_POST['submit'])){
        $block = new Blocker('', "***", ['en', 'nl']);
        $block->dictionary("../JS/filter.json");

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
            $errors['text'] = "Empty post submitted".$_POST['text'];
        }
    }
    else{
        header("location: ../postForm.php");
        exit;
    }

    if(count($errors) === 0){
        $post = new Post($title, $text, date('Y-m-d H:i:s'), $user->getId(), $user->getUsername());

        try{
            $dbh->addPost($post);
        }
        catch(Exception $e){
            $_SESSION['errors'] = ['post' => $e->getMessage().' at '.$e->getFile().$e->getLine()];
            header("location: ../postForm");
            exit;
        }
        $_SESSION['uploadSucces'] = "Post successfully uploaded!";
        header("location: ../postForm");
        exit;
    }
    else{
        $_SESSION['data'] = $_POST;
        $_SESSION['errors'] = $errors;
        header("location: ../postForm");
        exit;
    }
?>