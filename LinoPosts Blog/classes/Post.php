<?php
    namespace classes;
    class Post{
        private $title;
        private $text;
        private $dateTime;
        private $userId;
        private $username;
        private $databaseId;
        private bool $edited;

        public function __construct(?string $title = null, ?string $text = null, ?string $dateTime = null, ?int $userId = null, ?string $userName = null, ?int $databaseId = null, bool $edited = false){
            $this->title = $title;
            $this->text = $text;
            $this->dateTime = $dateTime;
            $this->userId = $userId;
            $this->username = $userName;
            $this->databaseId = $databaseId;
            $this->edited = $edited;
        }

        public function setTitle(string $title){
            $this->title = $title;
        }

        public function getTitle() : string{
            return $this->title;
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

        public function setEdited(bool $edited){
            $this->edited = $edited;
        }

        public function getEdited() : bool{
            return $this->edited;
        }
    }