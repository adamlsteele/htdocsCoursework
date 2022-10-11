<?php
//Include the connection.php file so that the class can be initialised
require "include/connection.php";

//Initialise the connection class
$connection = new Connection();

//Fetch the ResultID and question being allowed
//Initialise local variables for data that has been passed
$resultID = $_GET['resultID'];
$colNo = $_GET['col'];
$colName;

//Switch statement that takes the col number in the displayed table and provides the question number
switch($colNo) {
    case 7:
        $colName = "QuestionOneAnswer";
        break;
    case 9:
        $colName = "QuestionTwoAnswer";
        break;
    case 11:
        $colName = "QuestionThreeAnswer";
        break;
    case 13:
        $colName = "QuestionFourAnswer";
        break;
    case 15:
        $colName = "QuestionFiveAnswer";
        break;
}

//Create the string answer that they inputted and prepend 'MODID' to indicate that the marks for the question were modified
$answer = $_GET['answer'];
$modifiedAnswer = "MODIF".$answer;

//Initalise the connection class
$connection = new Connection;

//Change the answer so that it is the $modifiedAnswer and increment the marks for that particular result
$connection->query("UPDATE `result`
SET `".$colName."` = '".$modifiedAnswer."' 
WHERE `ResultID` = ".$resultID);

$connection->query("UPDATE `result`
SET `QuestionsCorrec`t = `QuestionsCorrect` + 1
WHERE `ResultID` = ".$resultID);

//Redirect back to the manage assignment place
header("Location:/teacher/manageAssignment.php?id=".$_GET['ret']);