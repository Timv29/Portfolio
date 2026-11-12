<?php
    require_once "vendor/autoload.php";
    use classes\{
        DatabaseHandler,
        Methods
    };
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
        header("Location: index.php");
    }

    try{
        $dbh = new DatabaseHandler($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);
    }
    catch(Exception $e){
        $databaseError = "Error loading posts </br>".$e->getMessage();
    }

    if(isset($dbh)){
        try{
            $posts = $dbh->getAllUserPosts($user->getId());  
        }
        catch(Exception $e){
            $databaseError =  "Error loading posts </br>".$e->getMessage();
        }
    }

    if(isset($_SESSION['errors'])){
        $errors = $_SESSION['errors'];
        unset($_SESSION['errors']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once "includes/head.php"?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.12.0/highlight.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.12.0/styles/atom-one-dark.min.css">
<body>
    <?php include_once "includes/header.php"?>
    <?php include_once "includes/sidebar.php"?>
    <div class="main posts">
        <?php
        if(isset($_SESSION['login'])){
            $user = unserialize($_SESSION['login']);
        }
        else{
            header("Location: index.php");
        }

        if(isset($posts)){
            if(isset($errors)){
                foreach($errors as $error){
                    echo '<p class="error">'.$error.'</p>';
                }
            }
            if(isset($_SESSION['deleteSucces'])){
                echo '<p>'.$_SESSION['deleteSucces'].'</p>';
                unset($_SESSION['deleteSucces']);
            }
            foreach($posts as $post){
                ?>
                    <div class="post">
                        <div class="postHead">
                            <h2 class="postTitle"><?= htmlspecialchars($post->getTitle()) ?></h2>
                            <p class="postAuthor">By: <?= $post->getUsername() ?> | <span class="postDate"> Posted: <?= $post->getDate() ?> <?= $post->getEdited() ? "(edited)" : "" ?></span></p>
                        </div>
                        <hr>
                        <div class="postContent">
                            <div class="postText"><?php 
                                if(strlen($post->getText()) > 750){
                                    echo strip_tags(substr($post->getText(), 0, 750),"<pre><a><br><p><span><ul><ol><li><u><em><strong><sup><sub>").'...';
                                }
                                else if(substr_count($post->getText(), PHP_EOL) >= 9){
                                    echo strip_tags(substr($post->getText(), 0, Methods::strposX($post->getText(), PHP_EOL, 9)),"<pre><a><br><p><span><ul><ol><li><u><em><strong><sup><sub>").'...';
                                }
                                else{
                                    echo strip_tags($post->getText(),"<pre><a><br><p><span><ul><ol><li><u><em><strong><sup><sub>");
                                }
                            ?></div>
                        </div>
                        <p class="readmore"><a href="post/<?= $post->getDatabaseId() ?>">Full post</a></p>  
                    </div>
                <?php
            }
        }
        else{
            echo $databaseError ?? '';
        }
        ?>
    </div>
</body>
<script>
    document.querySelectorAll('pre').forEach((el) => {
        hljs.highlightElement(el);
    });
</script>
</html>