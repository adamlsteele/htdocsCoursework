<?php
//This page creates an account for a user
require "include/header.php";

//Check that data is being sent via a HTTP POST request
if($_SERVER['REQUEST_METHOD'] != "POST") {
    die("Invalid request.");
}else {
    //Initialise local variables for data that has been passed
    $accountType = $_POST['accountType'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
}

//Validation for entered passwords
if($password !== $confirmPassword) {
    header("Location: /register.php?error=Passwords entered do not match");
}

//Initialise a new connection class
$connection = new Connection;

//Because the radio buttons are student and teacher it is possible to use concatenation in the SQL string.
//This means we can choose either the student table or teacher table.
$dbResult = $connection->getUserByEmail($email, $accountType);

//Validation that occurs if an account with the entered email already exists
if($dbResult->num_rows === 0) {
    $dbResult = $connection->createAccount($email, $username, $password, $accountType);
    header("Location: /?success=Account created, please login");
}else {
    header("Location: /register.php?error=An account with that email already exists");
}