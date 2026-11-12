<?php
    namespace classes;
    class User{
        private ?int $id;
        private ?string $username;
        private ?string $email;
        private ?string $password;
        private bool $commentNotifEnabled;
        private bool $replyNotifEnabled;

        public function __construct(?int $id = null, ?string $username = null, ?string $email = null, ?string $password = null, bool $commentNotifEnabled = true, bool $replyNotifEnabled = true){
            $this->id = $id;
            $this->username = $username;
            $this->email = $email;
            $this->password = $password;
            $this->commentNotifEnabled = $commentNotifEnabled;
            $this->replyNotifEnabled = $replyNotifEnabled;
        }

        public function setUsername(string $username){
            $this->username = $username;
        }

        public function getUsername() : string{
            return $this->username;
        }
        
        public function setEmail(string $email){
            $this->email = $email;
        }

        public function getEmail() : string{
            return $this->email;
        }
        
        public function setPassword(string $password){
            $this->password = $password;
        }

        public function getPassword() : string{
            return $this->password;
        }

        public function setId(int $id){
            $this->id = $id;
        }

        public function getId() : int{
            return $this->id;
        }

        public function setCommentNotifEnabled(bool $commentNotifEnabled){
            $this->commentNotifEnabled = $commentNotifEnabled;
        }

        public function getCommentNotifEnabled() : bool{
            return $this->commentNotifEnabled;
        }

        public function setReplyNotifEnabled(bool $replyNotifEnabled){
            $this->replyNotifEnabled = $replyNotifEnabled;
        }

        public function getReplyNotifEnabled() : bool{
            return $this->replyNotifEnabled;
        }

        public function __serialize(): array{
            return [
                "id" => $this->id,
                "username" => $this->username,
                "email" => $this->email,
                "commentNotifEnabled" => $this->commentNotifEnabled,
                "replyNotifEnabled" => $this->replyNotifEnabled
            ];
        }

        public function __unserialize(array $data): void{
            $this->id = $data['id'];
            $this->username = $data['username'];
            $this->email = $data['email'];
            $this->password = null;
            $this->commentNotifEnabled = $data['commentNotifEnabled'];
            $this->replyNotifEnabled = $data['replyNotifEnabled'];
        }
    }