<?php
//This page creates an account for a user
require "include/header.php";

//Check that data is being sent via a HTTP POST request
if($_SERVER['REQUEST_METHOD'] != "POST") {
    die("Invalid request.");
}else {
    //Initialise local variables for data that has been passed
    $class = $_POST['class'];
    $topic = $_POST['topic'];
    $dueDate = $_POST['dueDate'];
    $id = $_SESSION['accountID'];
}

//Check if the due date is less than the current date as an assignment cannot be set for the past
$date = new DateTime();
//Fetch the current date in the Y-m-d format and compare it to the entered date
$currentDate = $date->format('Y-m-d');
echo $currentDate;
echo $dueDate;
if($currentDate >= $dueDate) {
    header("Location: /teacher?error=Due date cannot be less than the current date");
}

//Intiailise a new connection class
$connection = new Connection;
//Create assignment
$result = $connection->createAssignment($class, $topic, $dueDate);

//Redirect back to the manage class page
//header("Location: /teacher/manageClass.php?id=".$class);