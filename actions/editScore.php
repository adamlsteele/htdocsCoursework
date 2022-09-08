<?php
require "include/connection.php";

$studentID = $_GET['id'];
$assignmentID = $_GET['a'];

$connection = new Connection;

$result = $connection->getAssignmentResult($assignmentID, $studentID);
$resultArray = mysqli_fetch_array($result);

echo '<p class="alert alert-primary">Student scored '.$resultArray[6].'/'.$resultArray[5].'</p>';

    for($i = 7; $i < 17; $i+=2) {
        $questionDetails = $connection->getQuestionByID($resultArray[$i])->fetch_assoc();
        echo $questionDetails['Question'];
        //Determine if the question was a written one as opposed to multiple choice
        if($questionDetails['QuestionAnswer2'] == null) {
            if($questionDetails['QuestionAnswer'] === $resultArray[$i+1]) {
                echo '</br><input class="alert alert-success" disabled name="'.$questionDetails['Question'].'" value="'.$resultArray[$i+1].'"/></br>';
            }else {
                //Any granted answers will have MODIF in front of their value to signify that a mark was awarded to the person.
                if(substr($resultArray[$i+1], 0, 5) != "MODIF") {
                    echo '</br><input class="alert alert-warning" disabled name="'.$questionDetails['Question'].'" value="'.$resultArray[$i+1].'"/></br>';
                    echo '<a href="/actions/allowMark.php?resultID='.$resultArray[0].'&col='.$i.'&answer='.$resultArray[$i+1].'&ret='.$assignmentID.'"class="badge badge-warning">Click to allow mark. Correct answer was "'.$questionDetails['QuestionAnswer'].'". </a></br></br>';
                } else {
                    echo '</br><input class="alert alert-success" disabled name="'.$questionDetails['Question'].'" value="'.substr($resultArray[$i+1], 5).'"/></br>';
                }
            }
        } else {
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