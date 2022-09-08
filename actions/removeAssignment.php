<?php
//This page creates an account for a user
require "include/header.php";

//Check that a teacher is authorised to use this page
if(!(isset($_SESSION['accountType']) && $_SESSION['accountType'] == 'teacher')) {
    header("Location: /?error=Please authenticate");
}


$assignmentID = $_GET['id'];

$connection = new Connection;
$connection->query("DELETE FROM assignment WHERE AssignmentID = ".$assignmentID);

header("Location: /teacher/manageClass.php?id=".$_GET['ret']);