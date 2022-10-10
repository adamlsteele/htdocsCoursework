<?php
//This page creates an account for a user
require "include/header.php";

//Check that data is being sent via a HTTP POST request
if($_SERVER['REQUEST_METHOD'] != "POST") {
    die("Invalid request.");
}else {
    //Initialise local variables for data that has been passed
    $className = $_POST['name'];
    $classDescription = $_POST['description'];
    $classColour = $_POST['classColour'];
    $id = $_SESSION['accountID'];
}

//Generate six digit code by picking a random letter out of the string characters
$characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$randomCode = "";

//Generate six digits one at a time and append them to an empty string
for ($i = 0; $i < 6; $i++) {
    $index = rand(0, strlen($characters) - 1);
    $randomCode .= $characters[$index];
}


//Initialise a new connection class
$connection = new Connection;

$dbResult = $connection->createClass($id, $className, $classDescription, $classColour, $randomCode);
header("Location: /")
?>