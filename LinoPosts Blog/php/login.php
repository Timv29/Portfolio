<?php
    require_once "vendor/autoload.php";
    use classes\DatabaseHandler;
    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable(__DIR__."/../");
    $dotenv->load();

    if(!isset($_SESSION)){ 
        session_start(); 
    } 
    $loginErrors = [];
    if(isset($_POST['login'])){
        if(!empty($_POST['username'])){
            $username = htmlspecialchars($_POST['username']);
        }
        else{
            $loginErrors['username'] = "No username";
        }

        if(!empty($_POST['password'])){
            $userpass = htmlspecialchars($_POST['password']);
        }
        else{
            $loginErrors['password'] = "No password";
        }

        if(count($loginErrors) === 0){
            $dbh = new DatabaseHandler($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);

            if($hashedPass = $dbh->getUserPassword($username)){
                if(hash_equals($hashedPass, crypt($userpass, $hashedPass))){
                    try{
                        $user = $dbh->getUser($username);
                        $_SESSION['login'] = serialize($dbh->getUser($username));
                    }
                    catch(Exception $e){
                        $loginErrors["login"] = $e->getMessage();
                    }

                }
                else{
                    $loginErrors['login'] = "Username or password incorrect";
                }
            }
            else{
                $loginErrors['login'] = "Username or password incorrect";
            }
        }

    }
    
    if(isset($_POST['logout'])){
        unset($_SESSION['login']);
    }