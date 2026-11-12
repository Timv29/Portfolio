<?php
    require_once "../vendor/autoload.php";
    use ConsoleTVs\Profanity\Classes\Blocker;
    use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
    use Symfony\Component\Mailer\Transport;
    use Symfony\Component\Mailer\Mailer;
    use Symfony\Component\Mime\Email;
    use Symfony\Component\Mime\Address;
    use classes\DatabaseHandler;
    use classes\Comment;
    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable(__DIR__."/../");
    $dotenv->load();

    if(!isset($_SESSION)){
        session_start();
    }

    function sendMail(string $mode, string $address, string $username, string $text, string $id){
        $transport = Transport::fromDsn('smtp://linoposts@gmail.com:'.$_ENV['GMAIL_PASS'].'@smtp.gmail.com:465');
        $mailer = new Mailer($transport);

        $email = new Email();
        $email->from(new Address('linoposts@gmail.com', 'LinoPosts'));
        $email->to($address);

        if($mode === "comment"){
            $email->subject("New Comment");
            $msg = '<h2>You got a new comment on your post!</h2>
            <p>'.$username.' commented: "'.$text.'"</p></br>
            <p>See it <a href="http://tvugt.rea.4u2c.nl/post/'.$id.'">here</a></p>';
        }
        elseif($mode === "reply"){
            $email->subject("New Reply");
            $msg = '<h2>You got a new reply to your comment!</h2>
            <p>'.$username.' replied: "'.$text.'"</p></br>
            <p>See it <a href="http://tvugt.rea.4u2c.nl/post/'.$id.'">here</a></p>';
        }

        $email->html($msg);

        try{
            $mailer->send($email);
        }
        catch(TransportExceptionInterface $e){
            $_SESSION['errors']['email'] = $e->getMessage();
            header("Location: ../post/".$id);
            exit;
        }
    }

    if(isset($_SESSION['postId'])){
        $postId = $_SESSION['postId'];
        unset($_SESSION['postId']);

        if(isset($_SESSION['login'])){
            $user = unserialize($_SESSION['login']); 
        }
        else{
            header("Location: ../post/".$postId);
            exit;
        }
    }
    else{
        header("Location: ../index");
        exit;
    }

    if(isset($_SESSION['replyCommentId'])){
        $replyCommentId = $_SESSION['replyCommentId'];
        unset($_SESSION['replyCommentId']);
    }

    $errors = [];
    if(isset($_POST['comment']) || isset($_POST['reply'])){
        if(strlen(trim($_POST['commentText']))){
            if(strlen($_POST['commentText']) <= 8000){
                $block = new Blocker($_POST['commentText'], "***", ['en', 'nl']);
                $block->dictionary("../filter.json");
                if($block->clean()){
                    $text = preg_replace("/(\n|\r|<p><\/p>|<\/br>){5,}/",'$1$1$1$1$1',$_POST['commentText']);
                }
                else{
                    $errors['commentText'] = "This comment contains innapropriate language";
                }
            }
            else{
                $errors['commentText'] = "Comment is too long";
            }

        }
        else{
            $errors['commentText'] = "Cannot reply with an empty comment";
        }
        //need to add moderation/blacklist
    }
    else{
        header("Location: ../post/".$postId);
        exit;
    }

    if(count($errors) === 0){
        $comment = new Comment($text, date('Y-m-d H:i:s'), $user->getId(), $_SERVER['REMOTE_ADDR'], $user->getUsername(), $postId, null, $replyCommentId ?? null, false, null);

        try{
            $dbh = new DatabaseHandler($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);
            if($dbh->insertComment($comment)){
                //send email
                $post = $dbh->getPostById($postId);
                $postUser = $dbh->getUser($post->getUsername());

                if($postUser->getCommentNotifEnabled() && $user->getUsername() !== $post->getUsername()){
                    sendMail("comment", $postUser->getEmail(), $user->getUsername(), $comment->getText(), $postId);
                }

                if(isset($replyCommentId)){
                    $replyComment = $dbh->getCommentById($replyCommentId);
                    $commentUser = $dbh->getUser($replyComment->getUsername());
                    
                    if($post->getUsername() !== $replyComment->getUsername() && $commentUser->getReplyNotifEnabled() && $user->getUsername() !== $commentUser->getUsername()){
                        sendMail("reply", $commentUser->getEmail(), $user->getUsername(), $comment->getText(), $postId);
                    }
                }

                header("Location: ../post/".$postId);
                exit;
            }
            else{
                $_SESSION['errors']['upload'] = "Error while uploading comment";
                header("Location: ../post/".$postId);
                exit;
            }         
        }
        catch(Exception $e){
            $_SESSION['errors']['database'] = 'Something went wrong: '.$e->getMessage();
            header("Location: ../post/".$postId);
            exit;
        }
    }
    else{
        $_SESSION['errors'] = $errors;
        header("Location: ../post/".$postId);
        exit;
    }
?>
