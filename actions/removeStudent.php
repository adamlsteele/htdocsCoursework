<?php
require "include/header.php";

//Check that a teacher is authorised to use this page
if(!(isset($_SESSION['accountType']) && $_SESSION['accountType'] == 'teacher')) {
    header("Location: /?error=Please authenticate");
}

$query = "UPDATE Student
SET ClassID = null
WHERE StudentID = ".$_GET['id'];

$connection = new Connection;
$connection->query($query);

header("Location:/");
?>