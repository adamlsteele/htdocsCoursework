<?php
require "include/header.php";

$query = "UPDATE Student
SET ClassID = null
WHERE StudentID = ".$_GET['id'];

$connection = new Connection;
$connection->query($query);

header("Location: /student/profile.php");