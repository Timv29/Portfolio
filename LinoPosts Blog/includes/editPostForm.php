<a href="post.php?postId=<?= $_GET['postId']?>"><div class="shadow"></div></a>
<div class="edit">
    <form id="editForm" method="post" action="php/editPost.php">
        <div class="formfield">
            <div>
                <label for="title">Title</label>
                <input maxlength="255" type="text" id="title" name="title" value="<?= $post->getTitle() ?? '' ?>" placeholder="Max. 255 characters">
            </div>
        </div>
        <div class="formfield postfield">
            <input type="hidden" id="htmlContent" value="<?= htmlspecialchars($post->getText()) ?>">
            <div id="text"></div>
        </div>
        <div class="buttons">
            <button type="submit" class="btn" name="edit">Edit Post</button>
            <a id="cancel" href="post/<?= $_GET['postId']?>">Cancel</a>
        </div>
    </form>
</div>