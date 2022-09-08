<?php
//This page creates an account for a user
require "include/header.php";

//Check that data is being sent via a HTTP POST request
if($_SERVER['REQUEST_METHOD'] != "POST") {
    die("Invalid request.");
}else {
    $accountType = $_POST['accountType'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
}

//Validation for entered passwords
if($password != $confirmPassword) {
    header("Location: /register.php?error=Passwords entered do not match");
}

//Check to see if an account already exists
$connection = new Connection;

//Because the radio button values are Student, Teacher (the same names of the databases)
//it is simple to use concatenation so that one query can handle both types of accounts
$dbResult = $connection->getUserByEmail($email, $accountType);

if($dbResult->num_rows === 0) {
    $dbResult = $connection->createAccount($email, $username, $password, $accountType);
    header("Location: /");
}else {
    header("Location: /register.php?error=An account with that email already exists");
}