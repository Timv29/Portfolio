<?php
    require_once "vendor/autoload.php";
    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable(__DIR__."/../");
    $dotenv->load();
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content= "A place to make and comment on blogs">
    <base href=<?= $_ENV['HTML_BASE'] ?>>
    <link rel="icon" type="image/x-icon" href="images/LinoPosts.png">
    <link href="css/style.css" rel="stylesheet">
    <title>LinoPosts</title>
</head>