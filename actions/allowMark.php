<?php
require "include/connection.php";

$connection = new Connection();

$resultID = $_GET['resultID'];
$colNo = $_GET['col'];
$colName;

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

$answer = $_GET['answer'];
$modifiedAnswer = "MODIF".$answer;

$connection = new Connection;

$connection->query("UPDATE Result
SET ".$colName." = '".$modifiedAnswer."' 
WHERE ResultID = ".$resultID);

$connection->query("UPDATE Result
SET QuestionsCorrect = QuestionsCorrect + 1
WHERE ResultID = ".$resultID);

header("Location:/teacher/manageAssignment.php?id=".$_GET['ret']);