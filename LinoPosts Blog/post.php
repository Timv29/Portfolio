<?php
    require_once "vendor/autoload.php";
    use classes\{
        DatabaseHandler
    };
    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    function repliesFromComment($resource, $commentId, $post){
        echo '<ul class="replies">';
        foreach($resource->getReplies($commentId) as $reply){
        ?>
        <li>
            <div class="replyComment">
                <div class="commentHead">
                    <p class="commentAuthor"><?= htmlspecialchars($reply->getUsername())?> <span class="dateTime">Posted: <?= $reply->getDateTime() ?></span></p>
                </div>
                <hr>
                <div class="commentContent">
                    <p class="commentText"><?= !$reply->getDeleted() ? htmlspecialchars($reply->getText()) : '<span class="deletedComment">['.htmlspecialchars($reply->getDeleteMsg()).']</span>' ?></p>
                    <?= (isset($_SESSION['login']) && (unserialize($_SESSION['login'])->getId() === $reply->getUserId() || unserialize($_SESSION['login'])->getId() === $post->getUserId() ) && !$reply->getDeleted() ? '<a href="post/'.$_GET['postId'].'/delComment='.$reply->getDatabaseId().'">Delete</a>' : '' )?>
                    <?= (isset($_SESSION['login']) && !$reply->getDeleted()) ? '<a href="post/'.$_GET['postId'].'/reply='.$reply->getDatabaseId().'">Reply</a>' : '' ?>
                </div>
            </div>
        <?php
        repliesFromComment($resource, $reply->getDatabaseId(), $post);
        echo '</li>';
        }
        echo '</ul>';
    }

    if(!isset($_SESSION)){
        session_start();
    }
    if(isset($_SESSION['login'])){
        $user = unserialize($_SESSION['login']);
    }

    if(isset($_SESSION['errors'])){
        $errors = $_SESSION['errors'];
        unset($_SESSION['errors']);
    }

    if(isset($_GET['postId'])){
        try{
            $dbh = new DatabaseHandler($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);
        }
        catch(Exception $e){
            $databaseError = "Error loading post </br>".$e->getMessage();
        }

        if(is_numeric($_GET['postId'])){
            $postId = $_GET['postId'];
            $_SESSION['postId'] = $_GET['postId'];
        }
        else{   
            $databaseError = "Invalid post ID";
            header("Location: ./index");
            exit;   
        }
    }

    if(isset($dbh) && isset($postId)){
        try{
            $post = $dbh->getPostById($postId);
            if(!isset($post)){
                $databaseError = "Post not found";
            } //Only check for _GET[edit] or _GET[delete] if post exists
            elseif(isset($_GET['edit'])){
                if(isset($user) && $user->getId() === $post->getUserId()){
                    $edit = True;
                }
                else{
                    unset($_SESSION['postId']);
                    header("Location: ../".$_GET['postId']);
                    exit;
                }
            }
            elseif(isset($_GET['delete'])){
                if(isset($user) && $user->getId() === $post->getUserId()){
                    $delete = True;
                }
                else{
                    unset($_SESSION['postId']);
                    header("Location: ../".$_GET['postId']);
                    exit;
                }
            }
            elseif(isset($_GET['delComment'])){
                $delComment = $dbh->getCommentById($_GET['delComment']);
                if(isset($user) && $user->getId() === $delComment->getUserId() && $delComment->getPostId() === intval($postId)){
                    $deleteComment = true;
                    $deleteCommentUser = "commenter";
                }
                elseif(isset($user) && $user->getId() === $post->getUserId() && $delComment->getPostId() === intval($postId)){
                    $deleteComment = true;
                    $deleteCommentUser = "poster";
                }
                else{
                    unset($_SESSION['postId']);
                    header("Location: ../".$_GET['postId']);
                    exit;
                }
            }
            elseif(isset($_GET['reply'])){
                $replyCommentId = $_GET['reply'];
                if(isset($user) && $dbh->getCommentById($replyCommentId)){
                    $_SESSION['replyCommentId'] = $replyCommentId;
                    $reply = true;
                }
                else{
                    unset($_SESSION['postId']);
                    header("Location: ../".$_GET['postId']);
                    exit;
                }
            }
        }
        catch(Exception $e){
            $databaseError = "Error loading post </br>".$e->getMessage();
        }
    }

    $comments = [];
    try{
        $comments = $dbh->getComments($postId ?? 0);
    }
    catch(Exception $e){
        $databaseError = "Error loading comments </br>".$e->getMessage();
    }
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once "includes/head.php"?>
<body>
    <?php include_once "includes/header.php" ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.12.0/highlight.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.12.0/styles/atom-one-dark.min.css">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <script src="JS/editPost.js" defer></script>
    <?php include_once "includes/sidebar.php" ?>
    <div class="main fullPost">
        <a href="index"><img id="backArrow" src="images/back.svg" width="40" height="40"></a>
        <?php 
        if(isset($errors)){
            foreach($errors as $error){
                echo '<p class="error">'.$error.'</p>';
            }
        }
        if(isset($post)){
            ?>
            <div class="post">
                <div class="postHead">
                    <h2 class="postTitle"><?= htmlspecialchars($post->getTitle()) ?? 'Title' ?></h2>
                    <p class="postAuthor">By: <?= $post->getUsername() ?? 'Author' ?> | <span class="postDate"> Posted: <?= $post->getDateTime() ?? 'dateTime' ?> <?= $post->getEdited() ? "(edited)" : "" ?></span></p>
                </div>
                <hr>
                <div class="postContent">
                    <div class="postText"><?= strip_tags($post->getText(), "<pre><a><br><p><span><ul><ol><li><u><em><strong><sup><sub>") ?? 'Post Content' ?></div>
                    <?=  (isset($_SESSION['login']) && unserialize($_SESSION['login'])->getId() === $post->getUserId() ? '<a href="post/'.$_GET['postId'].'/edit">Edit</a> <a href="post/'.$_GET['postId'].'/delete">Delete</a>' : '')?>
                </div>
            </div>
        <?php
        }
        else{
            echo '<p class="error">'.($databaseError ?? '').'</p>';
        }
        ?>
        <?php
            if(isset($edit) && $edit){
                include_once "includes/editPostForm.php";
            }
            elseif(isset($reply) && $reply){
                include_once "includes/replyCommentFrom.php";
            }
            elseif(isset($delete) && $delete){
                ?>
                <a href="post.php?postId=<?= $_GET['postId']?>"><div class="shadow"></div></a>
                <div class="delete">
                    <p id="deleteConfirmMsg">Are you sure you want to delete this post?</p>
                    <form id="deleteForm" method="post" action="php/deletePost.php">
                        <div class="buttons">
                            <button class="btn" type="submit" name="delete">Delete</button>
                            <a id="cancel" href="post/<?= $_GET['postId']?>">Cancel</a>
                        </div>
                    </form>
                </div>
                <?php
            }
            elseif(isset($deleteComment) && $deleteComment && isset($delComment)){
                $_SESSION['commentId'] = $delComment->getDatabaseId();
                $_SESSION['postAuthorId'] = $post->getUserId();
                ?>
                <a href="post/<?= $_GET['postId']?>"><div class="shadow"></div></a>
                <div class="delete">
                    <p id="deleteConfirmMsg">Are you sure you want to delete this comment?</p>
                    <form id="deleteForm" method="post" action="./php/deleteComment.php">
                        <?php
                            if(isset($deleteCommentUser) && $deleteCommentUser === 'poster'){
                        ?>
                            <label for="msg">Reason (optional)</label>
                            <select id="msg" name="msg">
                                <option selected value="">(None)</option>
                                <option>Discrimination/Hatespeech</option>
                                <option>Inappropriate language</option>
                                <option>Sexual Content</option>
                                <option>Doxxing</option>
                                <option>Other</option>
                            </select>
                        <?php
                            }
                        ?>
                        <div class="buttons">
                            <button class="btn" type="submit" name="delete">Delete</button>
                            <a id="cancel" href="post/<?= $_GET['postId']?>">Cancel</a>
                        </div>
                    </form>
                </div>
                <?php
            }
        ?>

        <!-- Comment form -->

        <div id="comments">
            <?php 
                if(isset($_SESSION['login'])){
            ?>
                <form id="commentForm" action="php/uploadComment" method="post">
                    <textarea name="commentText"></textarea>
                    <button type="submit" name="comment">Comment</button>
                </form>
            <?php
                }
            ?>

            <!-- Comments -->
            <ul id="allComments">
            <?php
                if(count($comments) > 0){
                    foreach($comments as $comment){
            ?>
                <li>
                    <div class="comment">
                        <div class="commentHead">
                            <p class="commentAuthor"><?= htmlspecialchars($comment->getUsername())?> <span class="dateTime">Posted: <?= $comment->getDateTime() ?></span></p>
                        </div>
                        <hr>
                        <div class="commentContent">
                            <p class="commentText"><?= !$comment->getDeleted() ? htmlspecialchars($comment->getText()) : '<span class="deletedComment">['.htmlspecialchars($comment->getDeleteMsg()).']</span>' ?></p>
                            <?= (isset($_SESSION['login']) && (unserialize($_SESSION['login'])->getId() === $comment->getUserId() || unserialize($_SESSION['login'])->getId() === $post->getUserId() ) && !$comment->getDeleted() ? '<a href="post/'.$_GET['postId'].'/delComment='.$comment->getDatabaseId().'">Delete</a>' : '' )?>
                            <?= (isset($_SESSION['login']) && !$comment->getDeleted()) ? '<a href="post/'.$_GET['postId'].'/reply='.$comment->getDatabaseId().'">Reply</a>' : '' ?>
                        </div>
                    </div>
            <?php
                    repliesFromComment($dbh, $comment->getDatabaseId(), $post);
                    }
                }
            ?>
                </li>
            </ul>
        </div>
    </div>
</body>
<script>
    document.querySelectorAll('pre').forEach((el) => {
        hljs.highlightElement(el);
    });
</script>
</html>