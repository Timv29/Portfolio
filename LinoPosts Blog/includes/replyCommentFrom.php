<a href="post.php?postId=<?= $_GET['postId']?>"><div class="shadow"></div></a>
<div class="reply">
    <form id="replyForm" method="post" action="php/uploadComment.php">
        <div class="formfield postfield">
            <textarea id="comment" name="commentText"></textarea>
        </div>
        <div class="buttons">
            <button class="btn" type="submit" name="reply">Reply</button>
            <a id="cancel" href="post/<?= $_GET['postId']?>">Cancel</a>
        </div>
    </form>
</div>