<?php
//This page creates an account for a user
require "include/header.php";

//Check that a teacher is authorised to use this page
if(!(isset($_SESSION['accountType']) && $_SESSION['accountType'] == 'teacher')) {
    header("Location: /?error=Please authenticate");
}

//Initialise local variables for data that has been passed
$assignmentID = $_GET['id'];

//Initialise a new connection class
$connection = new Connection;
//Delete an assignment
$connection->query("DELETE FROM assignment WHERE AssignmentID = ".$assignmentID);

//Redirect back to the manage class page
header("Location: /teacher/manageClass.php?id=".$_GET['ret']);