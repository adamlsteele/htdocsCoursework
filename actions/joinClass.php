<?php
require "include/header.php";

//Check that data is being sent via a HTTP POST request
if($_SERVER['REQUEST_METHOD'] != "POST") {
    die("Invalid request.");
}else {
    $classCode = $_POST['code'];
    $id = $_SESSION['accountID'];
}

$connection = new Connection;
$classDetails = $connection->getClassByCode($classCode);

if($classDetails->num_rows === 0) {
    header("Location:/student/profile.php?error=The code you entered does not match a class");
}else {
    $classDetailsArray = $classDetails->fetch_assoc();
    $classID = $classDetailsArray['ClassID'];
    
    $query = "UPDATE `Student`
    SET `ClassID` = $classID
    WHERE `StudentID` = ".$_SESSION['accountID'];

    $connection->query($query);
    header("Location:/student/profile.php");
}