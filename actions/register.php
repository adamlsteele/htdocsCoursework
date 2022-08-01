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
    header("Location: /register.php?error=Passwords entered do not match!");
}

//Check to see if an account already exists
$connection = new Connection;

//Because the radio button values are Student, Teacher (the same names of the databases)
//it is simple to use concatenation so that one query can handle both types of accounts
$dbResult = $connection->executeQuery("SELECT * FROM ".$accountType." WHERE Email = ?", "s", [$email]);

if($dbResult->num_rows === 0) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $connection->executeQuery("INSERT INTO ".$accountType."(Email, Username, Password) VALUES(?, ?, ?)", "sss", [$email, $username, $hashedPassword]);
}