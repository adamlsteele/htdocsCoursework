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

$dbResult = $connection->getUserByEmail($email, $accountType);
if($dbResult->num_rows != 0) {
    //Validate password
    $account = $dbResult->fetch_assoc();
    if(password_verify($password, $account['Password'])) {
        //Setup session variables
        $_SESSION['accountType'] = $accountType;
        switch($accountType) {
            case("student"):
                $_SESSION['accountID'] = $account['StudentID'];
            case("teacher"):
                $_SESSION['accountID'] = $account['TeacherID'];
        }
        header("Location: /");
    }else {
        header("Location: /?error=Invalid details");
    }
}else {
    header("Location: /?error=Account does not exist");
}

//Check for type of account
if($accountType === "student") {
    $dbResult = $connection->getUserByEmail($email, "student");
    //Check if an account with the entered email exists
    if($dbResult->num_rows != 0) {
        //Validate password
        $account = $dbResult->fetch_assoc();
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

echo $connection->error;

?>