<?php
//This page authenticates a user and provides them with session (global) variables.
require "include/header.php";

//Check that data is being sent via a HTTP POST request
if($_SERVER['REQUEST_METHOD'] != "POST") {
    die("Invalid request.");
}else {
    $accountType = $_GET['type'];
    $email = $_POST['email'];
    $password = $_POST['password'];
}

//Create a connection object to perform queries to database
$connection = new Connection;

//Check for type of account
if($accountType === "student") {
    $dbResult = $connection->executeQuery("SELECT * FROM student WHERE Email = ?", "s", $email);
    //Check if an account with the entered email exists
    if($dbResult->num_rows != 0) {
        //Validate password
        $account = $dbResult->get_result()->fetch_assoc();
        if(password_verify($password, $account['Password'])) {
            //Authentication valid
            //Setup session variables
            $_SESSION['accountType'] = "student";
            $_SESSION['accountID'] = $account['StudentID'];
            header("Location: /student");
        }else {
            header("Location: /?error=Invalid details");
        }
    }else {
        header("Location: /?error=Account does not exist");
    }
}

?>