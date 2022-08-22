<?php
require "include/header.php";

//Check that a teacher is authorised to use this page
if(!(isset($_SESSION['accountType']) && $_SESSION['accountType'] == 'teacher')) {
    header("Location: /?error=Please authenticate");
}

$classID = $_GET['id'];

//Check that the class in question belongs to the authenticated teacher by comparing the accountID
$connection = new Connection;
$classDetails = $connection->getClassByID($classID)->fetch_assoc();

if($classDetails['TeacherID'] === $_SESSION['accountID']) {
    $connection->deleteClass($classID);
    header("Location: /");
} else {
    die("Invalid. You do not own the class.");
}