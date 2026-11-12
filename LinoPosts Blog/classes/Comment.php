<?php
    namespace classes;
    class Comment{
        private $databaseId;
        private $text;
        private $dateTime;
        private $userId;
        private $username;
        private $postId;
        private $replyTo;
        private $deleted;
        private $deleteMsg;
        private $ip;

        public function __construct(?string $text = null, ?string $dateTime = null, ?int $userId = null, ?string $ip = null,?string $username = null, ?int $postId = null, ?int $databaseId = null, ?int $replyTo = null, bool $deleted = false, ?string $deleteMsg = null){
            $this->text = $text;
            $this->dateTime = $dateTime;
            $this->userId = $userId;
            $this->username = $username;
            $this->postId = $postId;
            $this->databaseId = $databaseId;
            $this->replyTo = $replyTo;
            $this->deleted = $deleted;
            $this->deleteMsg = $deleteMsg;
            $this->ip = $ip;
        }

        public function setText(string $text){
            $this->text = $text;
        }

        public function getText() : string{
            return $this->text;
        }

        public function setDateTime(string $dateTime){
            $this->dateTime = $dateTime;
        }

        public function getDateTime() : string{
            return $this->dateTime;
        }

        public function getDate() : string{
            return substr($this->dateTime, 0, 10);
        }

        public function getTime() : string{
            return substr($this->dateTime, 10);
        }

        public function setUserId(int $id){
            $this->userId = $id;
        }

        public function getUserId() : int{
            return $this->userId;
        }

        public function setUsername(string $username){
            $this->username = $username;
        }

        public function getUsername() : string{
            return $this->username;
        }

        public function setDatabaseId(int $databaseId){
            $this->databaseId = $databaseId;
        }

        public function getDatabaseId() : int{
            return $this->databaseId;
        }

        public function setPostId(int $postId){
            $this->postId = $postId;
        }

        public function getPostId() : int{
            return $this->postId;
        }

        public function setReplyTo(int $replyTo){
            $this->replyTo = $replyTo;
        }

        public function getReplyTo() : ?int{
            return $this->replyTo;
        }

        public function setDeleted(bool $deleted){
            $this->deleted = $deleted;
        }

        public function getDeleted() : bool{
            return $this->deleted;
        }

        public function setDeleteMsg(string $deleteMsg){
            $this->deleteMsg = $deleteMsg;
        }

        public function getDeleteMsg() : ?string{
            return $this->deleteMsg;
        }

        public function setIp(string $ip){
            $this->ip;
        }

        public function getIp() : ?string{
            return $this->ip;
        }
    }