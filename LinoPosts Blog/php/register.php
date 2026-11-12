<?php

require_once "../vendor/autoload.php";
use classes\DatabaseHandler;
use classes\User;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__."/../");
$dotenv->load();

if(!isset($_SESSION)){
    session_start();
}

$dbh = new DatabaseHandler($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);
$errors = [];
if(isset($_POST['register'])){
    if(!empty($_POST['username'])){
        if($dbh->checkUsernameAvailable($_POST['username'])){
            $username = ($_POST['username']);
        }
        else{
            $errors['username'] = "Username taken";
        }
    }
    else{
        $errors['username'] = "No username submitted";
    }

    if(!empty($_POST['email'])){
        if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            if($dbh->checkEmailAvailable($_POST['email'])){
                $email = ($_POST['email']);
            }
            else{
                $errors['email'] = "An account with this email exists already";
            }
        }
        else{
            $errors['email'] = "Invalid email";
        }
    }
    else{
        $errors['email'] = "No email submitted";
    }

    if(!empty($_POST['pass'])){
        $pass = ($_POST['pass']);
        if(!preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-_]).{8,}$/", $pass)){
            $errors['pass'] = "Password must be at least 8 characters and contain at least 1 uppercase letter, 1 lowercase letter, 1 number and 1 special char (#?!@$%^&*-_)";
        }
    }
    else{
        $errors['pass'] = "No password submitted";
    }

    if(!empty($_POST['confirmPass'])){
        if($_POST['confirmPass'] === $pass){
            $confirmPass = $_POST['confirmPass'];
        }
        else{
            $errors['confirmPass'] = "Passwords don't match";
        }
    }
    else{
        $errors['confirmPass'] = "No password submitted";
    }
}
else{
    header("Location: ../registerForm.php");
    exit;
}

if(count($errors) === 0){
    //insert account into database
    $hash = password_hash($pass, PASSWORD_BCRYPT);
    try{
        $dbh->insertUser(new User(null, $username, $email, $hash));
    }
    catch(Exception $e){
        $errors['pdo'] = $e->getMessage();
        $_SESSION['registerErrors'] = $errors;
        header("Location: ../registerForm.php?error");
        exit;
    }

    $_SESSION['succes'] = "User succesfully registered!";
    header("Location: ../registerForm.php?succes");
    exit;
}
else{
    $_SESSION['registerErrors'] = $errors;
    $_SESSION['registerData'] = $_POST;

    header("Location: ../registerForm.php?error");
    exit;
}