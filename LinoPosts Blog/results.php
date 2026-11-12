<?php
    require_once "vendor/autoload.php";
    use classes\DatabaseHandler;
    use classes\Methods;
    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    if(!isset($_SESSION)){
        session_start();
    }

    if(isset($_SESSION['login'])){
        $user = unserialize($_SESSION['login']);
    }
    
    if(isset($_GET['search'])){
        $search = $_GET['search'];

        try{
            $dbh = new DatabaseHandler($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);

            $posts = $dbh->getSearchPosts($search);
        }
        catch(Exception $e){
            $databaseError = $e->getMessage();
        }
    }
    else{
        header("Location: index");
    }
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once "includes/head.php"?>
<body>
    <?php include_once "includes/header.php"?>
    <?php include_once "includes/sidebar.php"?>
    <div class="main results">
        <?php
            if(isset($posts)){
                if(count($posts) > 0){
                    echo '<p>'.count($posts).' posts found that match with "'.$search.'"</p>';
                }
                else{
                    echo '<p>No posts found that match with "'.$search.'"</p>';
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
                                    else if(substr_count($post->getText(), PHP_EOL) >= 8){
                                        echo strip_tags(substr($post->getText(), 0, Methods::strposX($post->getText(), PHP_EOL, 8)),"<pre><a><br><p><span><ul><ol><li><u><em><strong><sup><sub>").'...';
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
</html>