<?php
//This page creates an account for a user
require "include/header.php";

//Check that data is being sent via a HTTP POST request
if($_SERVER['REQUEST_METHOD'] != "POST") {
    die("Invalid request.");
}else {
    $className = $_POST['name'];
    $classDescription = $_POST['description'];
    $classColour = $_POST['classColour'];
    $id = $_SESSION['accountID'];
}

//Generate six digit code
$characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$randomCode = "";
for ($i = 0; $i < 6; $i++) {
    $index = rand(0, strlen($characters) - 1);
    $randomCode .= $characters[$index];
}

echo $randomCode;

//Check to see if a class already exists
$connection = new Connection;

$dbResult = $connection->createClass($id, $className, $classDescription, $classColour, $randomCode);
header("Location: /")
?>