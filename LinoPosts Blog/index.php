<?php
    require_once "vendor/autoload.php";
    use classes\{
        DatabaseHandler,
        Post,
        Methods
    };
    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    try{
        $dbh = new DatabaseHandler($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);
    }
    catch(Exception $e){
        $databaseError = "Error loading posts </br>".$e->getMessage();
    }

    if(isset($dbh)){
        $countPosts = $dbh->countPosts();
        try{
            if(isset($_GET['sort'])){
                switch ($_GET['sort']){
                    case 'dateDesc':
                        $sortBy = ['p.dateTime', 'DESC'];
                        break;
                    case 'dateAsc':
                        $sortBy = ['p.dateTime', 'ASC'];
                        break;
                    case 'authorDesc':
                        $sortBy = ['u.username', 'DESC'];
                        break;
                    case 'authorAsc':
                        $sortBy = ['u.username', 'ASC'];
                        break;
                    default:
                        $sortBy = ['p.dateTime', 'DESC'];
                }
            }
            else{
                $sortBy = ['p.dateTime', 'DESC'];
            }

            if(isset($_GET['limit'])){
                if(is_numeric($_GET['limit']) && intval($_GET['limit'] > 0)){
                    $limit = $_GET['limit'];
                }
                elseif($_GET['limit'] === 'none'){
                    $limit = $countPosts;
                }
                else{
                    $limit = 5;
                }
            }
            else{
                $limit = 5;
            }

            $pages = intval(ceil($countPosts/$limit));

            if(isset($_GET['page']) && is_numeric($_GET['page']) && intval($_GET['page']) > 0 && intval($_GET['page']) <= $pages){
                $currentPage = intval(htmlspecialchars($_GET['page']));
            }
            else{
                $currentPage = 1;
            }

            if($pages > 5){
                if($currentPage < 4){
                    $pageNums = [2,3,4];
                }
                elseif($currentPage > $pages-3){
                    $pageNums = [$pages-3, $pages-2, $pages-1];
                }
                else{
                    $pageNums = [$currentPage-1, $currentPage, $currentPage+1];
                }
            }
            
            $offset = ($limit*$currentPage)-$limit;
            $posts = $dbh->getLimitedPosts($limit, $offset, $sortBy);
        }
        catch(Exception $e){
            $databaseError =  "Error loading posts </br>".$e->getMessage();
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<?php include_once "includes/head.php"?>
<body>
    <?php include_once "includes/header.php"?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.12.0/highlight.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.12.0/styles/atom-one-dark.min.css">
    <?php include_once "includes/sidebar.php"?>
    <div class="main posts">
        <?php
        if(isset($posts)){
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
                                    echo strip_tags(substr($post->getText(), 0, 750), "<pre><a><br><p><span><ul><ol><li><u><em><strong><sup><sub>").'...';
                                }
                                else if(substr_count($post->getText(), PHP_EOL) >= 8){
                                    echo strip_tags(substr($post->getText(), 0, Methods::strposX($post->getText(), PHP_EOL, 8)), "<pre><a><br><p><span><ul><ol><li><u><em><strong><sup><sub>").'...';
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

        if(isset($pages) && isset($currentPage)){
            echo '<ul class="pagination"><li '.($currentPage <= 1 ? 'class="endPage"><</li>' : '><a href="index?page='.($currentPage-1).(isset($_GET['sort']) ? '&sort='.$_GET['sort'] : '').(isset($_GET['limit']) ? '&limit='.$_GET['limit'] : '').'"><</a></li>');
                if(!isset($pageNums)){
                    for($i=1; $i<=$pages; $i++){
                        echo '<li><a class="pageNum '.($currentPage === $i ? 'active' : '').'" href="index?page='.$i.(isset($_GET['sort']) ? '&sort='.$_GET['sort'] : '').(isset($_GET['limit']) ? '&limit='.$_GET['limit'] : '').'">'.$i.'</a></li>';
                    }
                }
                else{
                    echo '<li><a class="pageNum '.($currentPage === 1 ? 'active' : '').'" href="index?page=1'.(isset($_GET['sort']) ? '&sort='.$_GET['sort'] : '').(isset($_GET['limit']) ? '&limit='.$_GET['limit'] : '').'">1</a></li>';
                    echo $currentPage > 3 ? '<li>...</li>': '';
                    echo '
                    <li><a class="pageNum '.($currentPage === $pageNums[0] ? 'active' : '').'" href="index?page='.$pageNums[0].(isset($_GET['sort']) ? '&sort='.$_GET['sort'] : '').(isset($_GET['limit']) ? '&limit='.$_GET['limit'] : '').'">'.$pageNums[0].'</a></li>
                    <li><a class="pageNum '.($currentPage === $pageNums[1] ? 'active' : '').'" href="index?page='.$pageNums[1].(isset($_GET['sort']) ? '&sort='.$_GET['sort'] : '').(isset($_GET['limit']) ? '&limit='.$_GET['limit'] : '').'">'.$pageNums[1].'</a></li>
                    <li><a class="pageNum '.($currentPage === $pageNums[2] ? 'active' : '').'" href="index?page='.$pageNums[2].(isset($_GET['sort']) ? '&sort='.$_GET['sort'] : '').(isset($_GET['limit']) ? '&limit='.$_GET['limit'] : '').'">'.$pageNums[2].'</a></li>';
                    echo $currentPage <= $pages-3 ? '<li>...</li>': '';
                    echo '<li><a class="pageNum '.($currentPage === $pages ? 'active' : '').'" href="index?page='.$pages.(isset($_GET['sort']) ? '&sort='.$_GET['sort'] : '').(isset($_GET['limit']) ? '&limit='.$_GET['limit'] : '').'">'.$pages.'</a></li>';
                }
            echo '<li '.($currentPage >= $pages ? 'class="endPage">></li<' : '><a href="index?page='.($currentPage+1).(isset($_GET['sort']) ? '&sort='.$_GET['sort'] : '').(isset($_GET['limit']) ? '&limit='.$_GET['limit'] : '').'">></a></li>').'</ul>';
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