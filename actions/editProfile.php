<?php
//This page edits a user profile and works with both types of accounts
require "include/header.php";

//Check that data is being sent via a HTTP POST request
if($_SERVER['REQUEST_METHOD'] != "POST") {
    die("Invalid request.");
}else {
    //Initialise local variables for data that has been passed
    $username = $_POST['username'];
    $newPassowrd = $_POST['newPassword'];
    $confirmNewPassword = $_POST['confirmNewPassword'];
    $password = $_POST['password'];
    $id = $_SESSION['accountID'];
}

//Grab the current user details
$connection = new Connection;
$currentUserDetails = $connection->getUserByID($id, $_SESSION['accountType'])->fetch_assoc();

//Validate the user before making any changes to their account
echo password_verify($password, $currentUserDetails);
if(!password_verify($password, $currentUserDetails['Password'])) {
    header("Location:/".$_SESSION['accountType']."/profile.php?error=Invalid password entered, cannot make changes");
    die();
}else {
   //Update the account username
    $query = "UPDATE ".$_SESSION['accountType']."
    SET Username = '".$username."'
    WHERE ".$_SESSION['accountType']."ID = ".$id;
    $connection->query($query);

    //Update the password if a new one is entered and matches a confirmation field
    if($newPassowrd != null ) {
        if($newPassowrd === $confirmNewPassword) {
            $hashedPassword = password_hash($newPassowrd, PASSWORD_DEFAULT);
            $query = "UPDATE ".$_SESSION['accountType']."
            SET Password = '".$hashedPassword."'
            WHERE ".$_SESSION['accountType']."ID = ".$id;
            header("Location:/".$_SESSION['accountType']."/profile.php?success=Password has been updated");
            die();
        }else{
            header("Location:/".$_SESSION['accountType']."/profile.php?error=The two new passwords you entered do not match");
            die();
        }
    } 
}

//Redirect back to the profile page
header("Location:/".$_SESSION['accountType']."/profile.php?success=Username has been updated");
