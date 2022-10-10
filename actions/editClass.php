<?php
//This page edits class details
require "include/header.php";

//Check that data is being sent via a HTTP POST request
if($_SERVER['REQUEST_METHOD'] != "POST") {
    die("Invalid request.");
}else {
    //Initialise local variables for data that has been passed
    $classID = $_POST['classID'];
    $className = $_POST['name'];
    $classDescription = $_POST['description'];
}

//Initalise a new connection class and update both the class name and class description
$connection = new Connection;
$query = "UPDATE Class
SET ClassName = '".$className."'
WHERE ClassID = ".$classID;
$connection->query($query);

$query = "UPDATE Class
SET ClassDescription = '".$classDescription."'
WHERE ClassID = ".$classID;
$connection->query($query);

//Redirect to the appropiate manage class page after handling SQL query
header("Location: /teacher/manageClass.php?id=".$_GET['ret']);