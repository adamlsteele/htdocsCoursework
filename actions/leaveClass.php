<?php
//Leave a class by updating the student record so that they are not attached to a class ID
require "include/header.php";

$query = "UPDATE `student`
SET `ClassID` = null
WHERE `StudentID` = ".$_GET['id'];

$connection = new Connection;
$connection->query($query);

//Redirect back to the edit profile page
header("Location: /student/profile.php");