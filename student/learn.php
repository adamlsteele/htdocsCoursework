<?php
require "include/header.php";

//Initialise a new connection class
$connection = new Connection;

//Determine if an assignment or general session is being completed
if(isset($_GET['topic'])) {
    //General session being completed
    $topicID = $_GET['topic'];
    $topicDetails = $connection->getTopicByID($topicID)->fetch_assoc();
}else if(isset($_GET['assignment'])) {
    //Assignment being completed
    $assignment = true;
    $assignmentID = $_GET['assignment'];
    $assignmentDetails = $connection->getAssignmentByID($assignmentID)->fetch_assoc();
    $topicDetails = $connection->getTopicByID($assignmentDetails['TopicID'])->fetch_assoc();
}
?>

<div class="m-1 row justify-content-center">
    <div class="col-lg-8">
        <?php if($assignment) {
            //Show a skip to test alert if the user is in an assignment session
            echo '<p class="alert alert-success m-4">You are currently completing an assignment. Would you like to skip to the <a href="/student/test.php?assignment='.$assignmentID.'">test</a>?</p>';
        }
        ?>
        <div class="p-4 m-2 card">
            <!-- Display Topic Details -->
            <h3>Learn</h3>
            <h5><?php echo $topicDetails['TopicName']; ?></h5>
            <p><?php echo $topicDetails['TopicContent']; ?></p>
        </div>
        <div class="p-4 m-2 card">
            <!-- Display Topic Examples -->
            <h3>Examples</h3>
            <p><?php echo $topicDetails['TopicExample']; ?></p>
        </div>
        <!-- Button To Go To Test -->
        <a class="btn btn-block btn-primary" href="<?php if($assignment) {echo "/student/test.php?assignment=".$assignmentID; }else{echo "/student/test.php?topic=".$topicID;} ?>">Test</a>
    </div>
</div>
