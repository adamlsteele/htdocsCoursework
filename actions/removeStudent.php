<?php
require "include/header.php";

//Check that a teacher is authorised to use this page
if(!(isset($_SESSION['accountType']) && $_SESSION['accountType'] == 'teacher')) {
    header("Location: /?error=Please authenticate");
}
//Remove a student from a class by updating and nullifying their classID
$query = "UPDATE student
SET ClassID = null
WHERE StudentID = ".$_GET['id'];

$connection = new Connection;
$result = $connection->query($query);

if(!$result) {
    echo "Error: " . mysqli_error($connection);
}

//Redirect back to the manage class page
header("Location:/teacher/manageClass.php?id=".$_GET['ret']);
?>