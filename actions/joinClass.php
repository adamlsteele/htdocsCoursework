<?php
//Include the connection file so a connection class can be initalised
require "include/header.php";

//Check that data is being sent via a HTTP POST request
if($_SERVER['REQUEST_METHOD'] != "POST") {
    die("Invalid request.");
}else {
    //Initialise local variables for data that has been passed
    $classCode = $_POST['code'];
    $id = $_SESSION['accountID'];
}

//Initialise a new connection class
$connection = new Connection;
//Fetch class details based on the code a user has entered
$classDetails = $connection->getClassByCode($classCode);

//Check the code is valid for a class
if($classDetails->num_rows === 0) {
    //Validation that indiciates an incorrectly entered code
    header("Location:/student/profile.php?error=The code you entered does not match a class");
}else {
    //Add the user to the class if the code matches one
    $classDetailsArray = $classDetails->fetch_assoc();
    $classID = $classDetailsArray['ClassID'];
    
    $query = "UPDATE `student`
    SET `ClassID` = $classID
    WHERE `StudentID` = ".$_SESSION['accountID'];

    $connection->query($query);
    //Redirect back to the edit profile page
    header("Location:/student/profile.php");
}