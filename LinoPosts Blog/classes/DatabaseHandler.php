<?php
    namespace classes;

    use Exception;
    use PDO;
    use PDOException;
    use classes\Post;
    use classes\User;
    use classes\Comment;

    class DatabaseHandler{
        private $resource;

        public function __construct(string $host, string $user, string $pass, string $database){
            try{
                $this->resource = new PDO('mysql:host='.$host.';dbname='.$database , $user, $pass);
                $this->resource->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch(PDOException $e){
                throw new Exception("Couldn't connect to database: ".$e->getMessage());
            }
        }

        public function __destruct(){
            $this->resource = null;
        }

        public function addPost(Post $post){
            $data= [$post->getTitle(), $post->getText(), $post->getDateTime(), $post->getUserId()];

            try{
                $stmt = $this->resource->prepare(
                    "INSERT INTO posts (title, text, dateTime, author_id) VALUES (?,?,?,?);"
                );
                $stmt->execute($data);
            }
            catch(PDOException $e){
                throw new Exception('PDO error: '.$e->getMessage());
            }
        }
        
        public function editPost(int $postId, string $title, string $text){
            try{
                $stmt = $this->resource->prepare(
                    "UPDATE posts SET title = ?, text = ?, edited = 1 WHERE id = ?;"
                );
                $stmt->bindParam(1, $title, PDO::PARAM_STR);
                $stmt->bindParam(2, $text, PDO::PARAM_STR);
                $stmt->bindParam(3, $postId, PDO::PARAM_INT);

                if($stmt->execute()){
                    return True;
                }
                else{
                    return False;
                }    
            }
            catch(PDOException $e){
                throw new Exception('PDO error: '.$e->getMessage());
            }
        }

        public function getAllPosts() : array{
            try{
                $stmt = $this->resource->prepare(
                    "SELECT p.id as databaseId, p.title, p.text, p.dateTime, p.author_id as userId, u.username, p.edited FROM posts p JOIN users u on p.author_id = u.id ORDER BY p.dateTime DESC;"
                );
                $stmt->execute();
                $posts = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'classes\Post');
                return $posts;
            }
            catch(PDOException $e){
                throw new Exception('PDO error: '.$e->getMessage());
            }
        }

        public function countPosts() : int{
            try{
                $stmt = $this->resource->query(
                    "SELECT count(DISTINCT id) as count FROM posts;"
                );
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['count'];
            }
            catch(PDOException $e){
                throw new Exception('PDO error: '.$e->getMessage());
            }
        }

        public function getLimitedPosts(int $limit, int $offset, array $sortBy) : array{
            try{
                $stmt = $this->resource->prepare(
                    "SELECT p.id as databaseId, p.title, p.text, p.dateTime, p.author_id as userId, u.username, p.edited FROM posts p JOIN users u on p.author_id = u.id ORDER BY ".$sortBy[0]." ".$sortBy[1]." LIMIT ? OFFSET ?;"
                );
                $stmt->bindParam(1, $limit, PDO::PARAM_INT);
                $stmt->bindParam(2, $offset, PDO::PARAM_INT);
                if($stmt->execute()){
                    $posts = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'classes\Post');
                }
                else{
                    $posts = [];
                }

                return $posts;
            }
            catch(PDOException $e){
                throw new Exception('PDO error: '.$e->getMessage());
            }
        }

        public function getSearchPosts(string $search) : array{
            try{
                $stmt = $this->resource->prepare(
                    "SELECT p.id as databaseId, p.title, p.text, p.dateTime, p.author_id as userId, u.username, p.edited FROM posts p JOIN users u on p.author_id = u.id WHERE MATCH(u.username) AGAINST(?) + MATCH(p.title, p.text) AGAINST(?);"
                );
                $stmt->bindParam(1, $search, PDO::PARAM_STR);
                $stmt->bindParam(2, $search, PDO::PARAM_STR);
                $stmt->execute();
                $posts = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'classes\Post');
                return $posts;
            }
            catch(PDOException $e){
                throw new Exception('PDO error: '.$e->getMessage());
            }
        }

        public function getAllUserPosts(int $userId) : array{
            try{
                $stmt = $this->resource->prepare(
                    "SELECT p.id as databaseId, p.title, p.text, p.dateTime, p.author_id as userId, u.username, p.edited FROM posts p JOIN users u on p.author_id = u.id WHERE p.author_id = ? ORDER BY p.dateTime DESC;"
                );
                $stmt->bindParam(1, $userId, PDO::PARAM_INT);
                $stmt->execute();
                $posts = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'classes\Post');
                return $posts;
            }
            catch(PDOException $e){
                throw new Exception('PDO error: '.$e->getMessage());
            }
        }

        public function getPostById(int $id) : ?Post{
            try{
                $stmt = $this->resource->prepare(
                    "SELECT p.id as databaseId, p.title, p.text, p.dateTime, p.author_id as userId, u.username, p.edited FROM posts p JOIN users u on p.author_id = u.id WHERE p.id = ?;"
                );
                $stmt->bindParam(1, $id, PDO::PARAM_INT);
                $stmt->execute();
                $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'classes\Post');

                if($post = $stmt->fetch()){
                    return $post;
                }
                else{
                    return null;
                }
            }
            catch(PDOException $e){
                throw new Exception('PDO error: '.$e->getMessage());
            }
        }

        public function getUserPassword(string $username) : ?string {
            try{
                $stmt = $this->resource->prepare(
                    "SELECT u.password FROM users u WHERE u.username = ?;"
                );

                $stmt->bindParam(1, $username, PDO::PARAM_STR);
                $stmt->execute();

                $pass = $stmt->fetch(PDO::FETCH_ASSOC);
                return $pass['password'] ?? NULL;
            }
            catch(PDOException $e){
                throw new Exception('PDO error: '.$e->getMessage());
            }
        }

        public function getUser(string $username): ?User{
            try{
                $stmt = $this->resource->prepare(
                    "SELECT id, username, password, email, commentNotifEnabled, replyNotifEnabled FROM users WHERE username = ?;"
                );
                $stmt->bindParam(1, $username, PDO::PARAM_STR);
                $stmt->execute();
                $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'classes\User');
                $user = $stmt->fetch();

                return $user;
            }
            catch(PDOException $e){
                throw new Exception('PDO error: '.$e->getMessage());
            }
        }

        public function checkUsernameAvailable(string $username): bool{
            try{
                $stmt = $this->resource->prepare(
                    "SELECT id FROM users WHERE username = ?;"
                );
                $stmt->bindParam(1, $username, PDO::PARAM_STR);
                $stmt->execute();
                if($stmt->fetch()){
                    return False;
                }
                else{
                    return True;
                }
            }
            catch(PDOException $e){
                throw new Exception('PDO error: '.$e->getMessage());
            }
        }

        public function checkEmailAvailable(string $email): bool{
            try{
                $stmt = $this->resource->prepare(
                    "SELECT id FROM users WHERE email = ?;"
                );
                $stmt->bindParam(1, $email, PDO::PARAM_STR);
                $stmt->execute();
                if($stmt->fetch()){
                    return False;
                }
                else{
                    return True;
                }
            }
            catch(PDOException $e){
                throw new Exception('PDO error: '.$e->getMessage());
            }
        }

        public function insertUser(User $user){
            try{
                $username = $user->getUsername();
                $pass = $user->getPassword();
                $email = $user->getEmail();

                $stmt = $this->resource->prepare(
                    "INSERT INTO users (username, password, email) VALUES (?,?,?);"
                );
                $stmt->bindParam(1, $username, PDO::PARAM_STR);
                $stmt->bindParam(2, $pass, PDO::PARAM_STR);
                $stmt->bindParam(3, $email, PDO::PARAM_STR);
                $stmt->execute();
            }
            catch(PDOException $e){
                throw new Exception('PDO error: '.$e->getMessage());
            }
        }

        public function deletePost(int $postId){
            try{
                $stmt = $this->resource->prepare("DELETE FROM posts WHERE id = ?;");
                $stmt->bindParam(1, $postId, PDO::PARAM_INT);
                if($stmt->execute()){
                    return True;
                }
                else{
                    return False;
                }                
            }
            catch(PDOException $e){
                throw new Exception('PDO error: '.$e->getMessage());
            }
        }

        public function insertComment(Comment $comment){
            try{
                $stmt = $this->resource->prepare("INSERT INTO comments (text, dateTime, author_id, author_ip, post_id, reply_to) VALUES (?,?,?,?,?,?);");
                $text = $comment->getText();
                $dateTime = $comment->getDateTime();
                $userId = $comment->getUserId();
                $postId = $comment->getPostId();
                $replyTo = $comment->getReplyTo();
                $ip = $comment->getIp();

                $stmt->bindParam(1, $text, PDO::PARAM_STR);
                $stmt->bindParam(2, $dateTime, PDO::PARAM_STR);
                $stmt->bindParam(3, $userId, PDO::PARAM_INT);
                $stmt->bindParam(4, $ip, PDO::PARAM_STR);
                $stmt->bindParam(5, $postId, PDO::PARAM_INT);
                $stmt->bindParam(6, $replyTo, PDO::PARAM_INT);
                if($stmt->execute()){
                    return True;
                }
                else{
                    return False;
                }
            }
            catch(PDOException $e){
                throw new Exception('PDO error: '.$e->getMessage());
            }
        }

        public function getComments(int $postId) : ?array{
            try{
                $stmt = $this->resource->prepare("SELECT c.id as databaseId, c.text, c.dateTime, c.author_id as userId, u.username, c.post_id as postId, c.reply_to as replyTo, c.deleted, c.delete_msg as deleteMsg
                FROM comments c JOIN users u ON c.author_id = u.id WHERE c.post_id = ? AND c.reply_to IS NULL ORDER BY c.dateTime DESC;");
                $stmt->bindParam(1, $postId, PDO::PARAM_INT);
                if($stmt->execute()){
                    $comments = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'classes\Comment');
                    return($comments);
                }
                else{
                    return [];
                }
            }
            catch(PDOException $e){
                throw new Exception('PDO error: '.$e->getMessage());
            }
        }

        public function getReplies(int $commentId) : ?array{
            try{
                $stmt = $this->resource->prepare("SELECT c.id as databaseId, c.text, c.dateTime, c.author_id as userId, u.username, c.post_id as postId, c.reply_to as replyTo, c.deleted, c.delete_msg as deleteMsg
                FROM comments c JOIN users u ON c.author_id = u.id WHERE c.reply_to = ? ORDER BY c.dateTime ASC;");
                $stmt->bindParam(1, $commentId, PDO::PARAM_INT);
                if($stmt->execute()){
                    $comments = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'classes\Comment');
                    return($comments);
                }
                else{
                    return [];
                }
            }
            catch(PDOException $e){
                throw new Exception('PDO error: '.$e->getMessage());
            }
        }

        public function getCommentById(int $commentId){
            try{
                $stmt = $this->resource->prepare("SELECT c.id as databaseId, c.text, c.dateTime, c.author_id as userId, u.username, c.post_id as postId, c.reply_to as replyTo, c.deleted, c.delete_msg as deleteMsg
                FROM comments c JOIN users u ON c.author_id = u.id WHERE c.id = ?;");
                $stmt->bindParam(1, $commentId, PDO::PARAM_INT);
                if($stmt->execute()){
                    $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'classes\Comment');
                    $comment = $stmt->fetch();
                    return $comment;
                }
                else{
                    return null;
                }
            }
            catch(PDOException $e){
                throw new Exception('PDO error: '.$e->getMessage());
            }
        }

        public function deleteComment(int $commentId, ?string $msg = "Comment Deleted"){
            try{
                $stmt = $this->resource->prepare("UPDATE comments c SET c.deleted = True, c.delete_msg = ? WHERE c.id = ?");
                $stmt->bindParam(1, $msg, PDO::PARAM_STR);
                $stmt->bindParam(2, $commentId, PDO::PARAM_INT);

                if($stmt->execute()){
                    return True;
                }
                else{
                    return False;
                }
            }
            catch(PDOException $e){
                throw new Exception('PDO error: '.$e->getMessage());
            }
        }

        public function updateSettings(int $userId, bool $commentNotifEnabled, bool $replyNotifEnabled){
            try{
                $stmt = $this->resource->prepare("UPDATE users u SET u.commentNotifEnabled = ?, u.replyNotifEnabled = ? WHERE u.id = ?");
                $stmt->bindParam(1, $commentNotifEnabled, PDO::PARAM_BOOL);
                $stmt->bindParam(2, $replyNotifEnabled, PDO::PARAM_BOOL);
                $stmt->bindParam(3, $userId, PDO::PARAM_INT);

                if($stmt->execute()){
                    return True;
                }
                else{
                    return False;
                }
            }
            catch(PDOException $e){
                throw new Exception('PDO error: '.$e->getMessage());
            }
        }
    }