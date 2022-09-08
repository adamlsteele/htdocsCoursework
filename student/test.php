<?php
require "include/header.php";

$connection = new Connection;

$topicID;

//Determine if an assignment or general session is being completed
if(isset($_GET['topic'])) {
    //General session being completed
    $topicID = $_GET['topic'];
    //Grab topic details
    $topicDetails = $connection->getTopicByID($topicID)->fetch_assoc();
}else if(isset($_GET['assignment'])) {
    //Assignment being completed
    $assignment = true;
    $assignmentID = $_GET['assignment'];
    //Grab assignment details and topic infomration for the assignment
    $assignmentDetails = $connection->getAssignmentByID($assignmentID)->fetch_assoc();
    $topicDetails = $connection->getTopicByID($assignmentDetails['TopicID'])->fetch_assoc();
    $topicID = $topicDetails['TopicID'];
}

$questions = $connection->getQuestionsByID($topicDetails['TopicID']);
$questionsArray = mysqli_fetch_array($questions);

//If a post request has been sent, a topic is waiting to be marked
if($_SERVER['REQUEST_METHOD'] == "POST"){
    //Loop through each question
    $marks = 0;
    $attemptedQuestionsArray = array();
    foreach($_POST as $key => $value) {
        array_push($attemptedQuestionsArray, array($key, $value));
        $questionDetails = $connection->getQuestionByID($key)->fetch_assoc();
        if($questionDetails['QuestionAnswer'] === $value) {
            echo '<p class="alert alert-success">Question: '.$questionDetails['Question'].' You correctly answered: '.$value.'</p>';
            $marks++;
        }else {
            //Determine if the question is a text input. If it is and the test was an assignment, indicate that the teacher can update the mark.
            if($questionDetails['QuestionAnswer2'] === null && $assignment) {
                echo '<p class="alert alert-warning">Question: '.$questionDetails['Question'].' You incorrectly answered: '.$value.'.</br>The correct answer was: '.$questionDetails['QuestionAnswer'].'</br>As this was a text input for an assignment, your teacher is able to override your mark if you believe your answer was correct.</p>';
            }else {
                echo '<p class="alert alert-danger">Question: '.$questionDetails['Question'].' You incorrectly answered: '.$value.'.</br>The correct answer was: '.$questionDetails['QuestionAnswer'].'</p>';
            }
        }
    }

    //Show an overall results message and button to go back home
    echo '<p class="alert alert-primary">You totalled '.$marks.'/5</p>';
    echo '<a class="btn btn-primary btn-block" href="/student/">Go Home</a>';

    //Insert a results record
    if($assignment) {
        $connection->addAssignmentResult($assignmentID, $topicID, $_SESSION['accountID'], 5, $marks, $attemptedQuestionsArray[0][0], $attemptedQuestionsArray[0][1], $attemptedQuestionsArray[1][0], $attemptedQuestionsArray[1][1], $attemptedQuestionsArray[2][0], $attemptedQuestionsArray[2][1], $attemptedQuestionsArray[3][0], $attemptedQuestionsArray[3][1], $attemptedQuestionsArray[4][0], $attemptedQuestionsArray[4][1]);
    }else {
        $connection->addTopicResult($topicID, $_SESSION['accountID'], 5, $marks, $attemptedQuestionsArray[0][0], $attemptedQuestionsArray[0][1], $attemptedQuestionsArray[1][0], $attemptedQuestionsArray[1][1], $attemptedQuestionsArray[2][0], $attemptedQuestionsArray[2][1], $attemptedQuestionsArray[3][0], $attemptedQuestionsArray[3][1], $attemptedQuestionsArray[4][0], $attemptedQuestionsArray[4][1]);
    }

    $connection->query("UPDATE student SET QuestionsAnswered = QuestionsAnswered + 5
    WHERE StudentID = ".$_SESSION['accountID']);
    $connection->query("UPDATE student SET QuestionsCorrect = QuestionsCorrect + ".$marks."
    WHERE StudentID = ".$_SESSION['accountID']);



    //Stop the rest of code running as we want to show a results page and not quiz the user again.
    die;
}

?>

<div class="m-1 row justify-content-center">
    <div class="col-lg-8">
        <?php if($assignment) {
            echo '<p class="alert alert-primary m-4">This test is for an assignment due in on: '.$assignmentDetails['Date'].'. You can attempt this test once. </br>Your teacher is able to override the marks for your text inputs if necessary.</p>';
        }
        ?>
        <div class="p-4 m-2 card">

            <form action="<?php if($assignment) {echo "/student/test.php?assignment=".$assignmentID; }else{echo "/student/test.php?topic=".$topicID;} ?>" method="POST">
                <?php
                //Stores the amount of questions we can ask
                $max = mysqli_num_rows($questions);
                //Stores the indexes of the questions we are going to ask
                $questionBank = [];
                //Randomly select half of the questions to be asked
                for($i = 1; $i <= ($max/2); $i++) {
                    //Generate random number until a unique one is generated not already in the question bank
                    $randomNumber = rand(1, $max);
                    while(in_array($randomNumber, $questionBank)) {
                        $randomNumber = rand(1, $max);
                    }
                    //Add the unique random number to the question bank
                    array_push($questionBank, $randomNumber);
                }
                //Counter that tracks progress in the for loop
                $counter = 1;
                foreach($questions as $question) {
                    //Checks if the question was randomly selected in the question bank
                    if(!in_array($counter, $questionBank)) {
                        $counter++;
                        continue;
                    }
                    //Differientiates multiple choice and text entry questions
                    if($question['QuestionAnswer2'] === null) {
                        echo '<input required placeholder="'.$question['Question'].'" class="form-control mb-4 id="'.$question['Question'].'" name="'.$question['QuestionID'].'"/>';
                    }else {
                        echo $question['Question'];
                        //By placing the radio buttons in an array it can be shuffled for random answers
                        $radioButtons = array('<div class="custom-control custom-radio"><input value="'.$question['QuestionAnswer'].'" type="radio" class="custom-control-input mb-4" id="'.$question['Question'].'" name="'.$question['QuestionID'].'" required><label class="custom-control-label" for="'.$question['Question'].'">'.$question['QuestionAnswer'].'</label></div>',
                        '<div class="custom-control custom-radio"><input value="'.$question['QuestionAnswer2'].'" type="radio" class="custom-control-input mb-4" id="'.$question['Question'].'" name="'.$question['QuestionID'].'" required><label class="custom-control-label" for="'.$question['Question'].'">'.$question['QuestionAnswer2'].'</label></div>',
                        '<div class="custom-control custom-radio"><input value="'.$question['QuestionAnswer3'].'" type="radio" class="custom-control-input mb-4" id="'.$question['Question'].'" name="'.$question['QuestionID'].'" required><label class="custom-control-label" for="'.$question['Question'].'">'.$question['QuestionAnswer3'].'</label></div>',
                        '<div class="custom-control custom-radio"><input value="'.$question['QuestionAnswer4'].'" type="radio" class="custom-control-input mb-4" id="'.$question['Question'].'" name="'.$question['QuestionID'].'" required><label class="custom-control-label" for="'.$question['Question'].'">'.$question['QuestionAnswer4'].'</label></div>');
                        shuffle($radioButtons);
                        foreach($radioButtons as $button) {
                            echo $button;
                        }
                    }
                    $counter++;
                }
                ?>
                <button class="btn btn-block btn-primary" action="submit">Submit</button>
            </form>
        </div>
    </div>
</div>

<?php
require "./include/footer.php";
?>