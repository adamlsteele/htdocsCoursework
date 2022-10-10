<?php
//Fetch the connection file so that a new connection can be initalised
require "include/connection.php";

//Fetch the studentID to be updated and the assignmentID
$studentID = $_GET['id'];
$assignmentID = $_GET['a'];

//Initialise connection class
$connection = new Connection;

//Fetch the results of the student and place these in an array
$result = $connection->getAssignmentResult($assignmentID, $studentID);
$resultArray = mysqli_fetch_array($result);

//The edit score page runs as an AJAX query.
//This means that the data below updates visually on the page, but the whole page does not have to be reloaded.

//Output the score in an alert box
echo '<p class="alert alert-primary">Student scored '.$resultArray[6].'/'.$resultArray[5].'</p>';

    //Loop from 7-17 incrementing in 2s. This is because the question IDs are stored following the answer inputted.
    //We are only interested in the question ID and so we increment twice to skip to the next ID.
    for($i = 7; $i < 17; $i+=2) {
        //Fetch the details of a particular question, including the proper answer
        $questionDetails = $connection->getQuestionByID($resultArray[$i])->fetch_assoc();
        //Determine if the question was a written one as opposed to multiple choice by seeing if multiple question options are stored in the database
        if($questionDetails['QuestionAnswer2'] == null) {
            //If the written question input matched the answer, output a success box
            if($questionDetails['QuestionAnswer'] === $resultArray[$i+1]) {
                echo '</br><input class="alert alert-success" disabled name="'.$questionDetails['Question'].'" value="'.$resultArray[$i+1].'"/></br>';
            }else {
                //Any granted answers will have MODIF in front of their value to signify that a mark was awarded to the person.
                //If the answer does not match the correct answer but has been allowed before it will also show a success box
                if(substr($resultArray[$i+1], 0, 5) != "MODIF") {
                    //If the inputted answer does not have MODIF in front, show a warning box allowing the teacher to grant the mark
                    echo '</br><input class="alert alert-warning" disabled name="'.$questionDetails['Question'].'" value="'.$resultArray[$i+1].'"/></br>';
                    echo '<a href="/actions/allowMark.php?resultID='.$resultArray[0].'&col='.$i.'&answer='.$resultArray[$i+1].'&ret='.$assignmentID.'"class="badge badge-warning">Click to allow mark. Correct answer was "'.$questionDetails['QuestionAnswer'].'". </a></br></br>';
                } else {
                    echo '</br><input class="alert alert-success" disabled name="'.$questionDetails['Question'].'" value="'.substr($resultArray[$i+1], 5).'"/></br>';
                }
            }
        } else {
            //If the question was multiple choice, either show a success box or danger box.
            echo '</br><p class="alert alert-';
            if($questionDetails['QuestionAnswer'] === $resultArray[$i+1]){
                echo 'success'; 
            }else {
                echo 'danger';
            }
            echo '">'.$resultArray[$i+1].'.</p>';
        }
    }

?>