<?php
//This page creates an account for a user
require "include/header.php";

//Check that data is being sent via a HTTP POST request
if($_SERVER['REQUEST_METHOD'] != "POST") {
    die("Invalid request.");
}else {
    $class = $_POST['class'];
    $topic = $_POST['topic'];
    $dueDate = $_POST['dueDate'];
    $id = $_SESSION['accountID'];
}

//Check if the due date is less than the current date as an assignment cannot be set for the past
$date = new DateTime();
$currentDate = $date->format('Y-m-d');
if($currentDate >= $dueDate) {
    header("Location: /teacher?error=Due date cannot be less than the current date");
}

//Insert an assignment into the database
$connection = new Connection;
$result = $connection->createAssignment($class, $topic, $dueDate);