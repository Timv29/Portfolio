<?php
    if(!isset($_SESSION)){
        session_start();
    }
    
    if(isset($_SESSION['errors'])){
        $postErrors = $_SESSION['errors'];
        $data = $_SESSION['data'];

        unset($_SESSION['errors']);
        unset($_SESSION['data']);
    }

    if(isset($_SESSION['uploadSucces'])){
        $succes = $_SESSION['uploadSucces'];
        unset($_SESSION['uploadSucces']);
    }

?>
<!DOCTYPE html>
<html lang="en">
<?php include_once "includes/head.php"?>
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.12.0/highlight.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.12.0/styles/atom-one-dark.min.css">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <script src="JS/textEditor.js" defer></script>
</head>
<body>
    <?php include_once "includes/header.php" ?>
    <?php include_once "includes/sidebar.php" ?>
    <div class="main createPost">
        <?php
            if(isset($_SESSION['login'])){
                $user = unserialize($_SESSION['login']);
        ?>
        <a href="index"><img id="backArrow" src="images/back.svg" width="40" height="40"></a>
        <form id="postForm" method="post" action="php/uploadPost.php">
            <?= isset($succes) ? '<h3>'.$succes.'</h3>' : '' ?>
            <div class="formfield">
                <?= isset($postErrors['title']) ? '<p class="error">'.$postErrors['title'].'</p>' : '' ?>
                <div>
                    <label for="title">Title</label>
                    <input maxlength="255" type="text" id="title" name="title" value="<?= $data['title'] ?? '' ?>" placeholder="Max. 255 characters">
                </div>
            </div>
            <div class="formfield postfield">
                <?= isset($postErrors['text']) ? '<p class="error">'.$postErrors['text'].'</p>' : '' ?>
                <div id="editor"></div>
            </div>
            <?= isset($postErrors['post']) ? '<p class="error">'.$postErrors['post'].'</p>' : '' ?>
            <button class="btn" type="submit" name="submit">Upload Post</button>
        </form>
        <?php
            }
            else{
        ?>

        <p>You must be logged in to create a post</p>
        <?php
            }
        ?>
    </div>  
</body>
</html>