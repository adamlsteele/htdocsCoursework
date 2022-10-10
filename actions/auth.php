<?php
//This page authenticates a user and provides them with session (global) variables.
require "include/header.php";

//Check that data is being sent via a HTTP POST request
if($_SERVER['REQUEST_METHOD'] != "POST") {
    die("Invalid request.");
}else {
    //Initialise local variables for data that has been passed
    $accountType = $_GET['type'];
    $email = $_POST['email'];
    $password = $_POST['password'];
}

//Create a connection object to perform queries to database
$connection = new Connection;

$dbResult = $connection->getUserByEmail($email, $accountType);
if($dbResult->num_rows != 0) {
    //Validate password
    $account = $dbResult->fetch_assoc();
    if(password_verify($password, $account['Password'])) {
        //Setup session variables (global variables)
        $_SESSION['accountType'] = $accountType;
        switch($accountType) {
            case("student"):
                $_SESSION['accountID'] = $account['StudentID'];
            case("teacher"):
                $_SESSION['accountID'] = $account['TeacherID'];
        }
        //Redirect following authentication
        header("Location: /");
    }else {
        //Verification that redirects if the incorrect password was entered
        header("Location: /?error=Invalid details");
    }
}else {
    //Verification that redirects if the account does not exist
    header("Location: /?error=Account does not exist");
}
//If the database failed an SQL request, output that error
echo $connection->error;
?>