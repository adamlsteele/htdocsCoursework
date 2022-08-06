<?php
require("include/header.php");

//Redirect to login page if there are no authenticated sessions for the student account type
if(!(isset($_SESSION['accountType']) && $_SESSION['accountType'] == 'student')) {
    header("Location: /?error=Please authenticate");
}


//Grab account details for ID stored within session variable
$connection = new Connection();
$userDetails = $connection->getUserByID($_SESSION['accountID'], "student")->fetch_assoc();

$username = $userDetails['Username'];

//Grab class details for a user if they are in a class
$classDetails = -1;

if($userDetails['ClassID'] !== null) {
    $classDetails = $connection->getClassByID($userDetails['ClassID'])->fetch_assoc();
    $assignmentDetails = $connection->getAssignmentsByClassID(($userDetails['ClassID']));
}

//Assign variables for statistics
$questionsAnswered = $userDetails['Questions Answered'];
$questionsCorrect = $userDetails['Questions Correct'];

//Work out total percentage accuracy to 2 decimal places so that it is easy to read
$totalQuestionsAccuracy = number_format(($questionsCorrect/$questionsAnswered)*100, 2);

?>

<div class="m-1 row justify-content-center">
    <div class="col-lg-6">
        <div class="card p-4 m-2">
            <!-- Welcome message -->
            <h3>Hello, <?php echo $username; ?></h3>
            <h5>Student Dashboard</h5>
            <p>Access assignments, view progress and start your own revision sessions here.</p>
        </div>
        <div class="card p-4 m-2">
            <h5>Your Class</h5>
            <!-- Class details -->
            <?php
            if($classDetails === -1) {
                echo '<p class="alert alert-warning"><strong>You are not currently in a class.</strong></br>Join a class to recieve and complete assignments set by your teacher. Ask them for a class code and enter this in the <a class="alert-link" href="/student/profile">profile</a> page.</p>';
            }else {
                echo '<table class="table"><thead><tr><th scope="col">Class Name</th><th scope="col">Class Description</th></tr></thead><tbody><tr><td>'.$classDetails['Class Name'].'</td><td>'.$classDetails['Class Description'].'</td></tr></tbody></table>';
                echo '<h5 class="mt-2">Assignments</h5><table class="table"><thead><tr><th scope="col">Date Due</th><th scope="col">Topic</th><th scope="col">Percentage</th></tr></thead><tbody>';
                foreach($assignmentDetails as $assignment) {
                    echo '<tr><td>';
                    echo $assignment['Date'];
                    echo '</td><td>';
                    echo $assignment['topic.Topic Name'];
                    echo '</td>';
                }
                echo '</tbody></table>';
            }
            ?>
        </div>
        <div class="card p-4 m-2">
            <h5>Learn</h5>
            <p>Learn topics outside of a class-set assignment.</p>
            <button class="btn btn-primary">Learn</button>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-4 m-2">
            <h5>Your progress</h5>
            <!-- Cards showing specific statistics -->
            <div class="container text-center">
                <div class="row m-1 p-4">
                    <div class="col m-2"><h5><?php echo $questionsAnswered; ?></h5> <span class="p-2">Total Questions Answered</span></div>
                    <div class="col m-2"><h5><?php echo $questionsCorrect; ?></h5> <span class="p-2">Total Questions Correct</span></div>
                    <div class="col m-2"><h5><?php echo $totalQuestionsAccuracy; ?>%</h5> <span class="p-2">Average Question Accuracy</span></div>
                </div>
            </div>

            <!-- Recent sessions list -->
            <h5>Recent Sessions</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Date Completed</th>
                        <th scope="col">Topic Name</th>
                        <th scope="col">Question Accuracy</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>12/07/2020</td>
                        <td>Arrays</td>
                        <td>62.34%</td>
                    </tr>
                    <tr>
                        <td>12/07/2020</td>
                        <td>Arrays</td>
                        <td>62.34%</td>
                    </tr>
                    <tr>
                        <td>12/07/2020</td>
                        <td>Arrays</td>
                        <td>62.34%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require("include/footer.php");
?>