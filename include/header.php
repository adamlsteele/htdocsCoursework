<?php
session_start();
require "include/connection.php";
error_reporting(E_ERROR | E_PARSE);
?>

<head>
    <!--Title dynamically updates depending on the request URL-->
    <title>Cloud Coding | <?php echo $_SERVER["REQUEST_URI"]; ?></title>
    <!--Bootstrap styling files-->
    <link rel="icon" href="/img/mdb-favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.2/css/all.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap"/>
    <link rel="stylesheet" href="/css/mdb.min.css" />
    <link rel="stylesheet" href="/css/custom.css" />
</head>

<!--Navigation bar-->
<nav class="navbar navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand">Cloud Coding<?php if(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) === "/student/learn.php"){echo '| Learn Mode';}?></a>

        <ul class="navbar-nav"></ul>
        <div class="d-flex align-items-center">
            <?php
            //If the page is the root (homepage) do not display these navigation buttons
            if(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) != "/" && parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) != "/register.php" && parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) != "/student/learn.php" && parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) != "/student/test.php") {
                echo '
                <a href="/" class="btn btn-primary m-1">Home</a>
                <a href="/'.$_SESSION['accountType'].'/profile.php" class="btn btn-primary m-1">Profile</a>
                <a href="/actions/endSession.php" class="btn btn-danger m-1">Sign out</a>
                ';
            }
            ?>    
        </div>
        
    </div>
</nav>