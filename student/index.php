<?php
require("include/header.php");

//Redirect to login page if there are no authenticated sessions for the student account type
if(!(isset($_SESSION['accountType']) && $_SESSION['accountType'] == 'student')) {
    header("Location: /?error=Please authenticate");
}


//Grab account details for ID stored within session variable

//Initialise a new connection class
$connection = new Connection();
$userDetails = $connection->getUserByID($_SESSION['accountID'], "student")->fetch_assoc();

//Initialise local variable for data fetched from the database
$username = $userDetails['Username'];

//Grab class details for a user if they are in a class
$classDetails = -1;
if($userDetails['ClassID'] !== null) {
    $classDetails = $connection->getClassByID($userDetails['ClassID'])->fetch_assoc();
    $assignmentDetails = $connection->getAssignmentsByClassID(($userDetails['ClassID']));
}

//Calculate the questions answered and questions correct based on the totals from all topics
$results = $connection->getResultsById($_SESSION['accountID']);
$questionsAnswered = 0;
$questionsCorrect = 0;
foreach($results as $resultDetails) {
    $questionsAnswered+= $resultDetails['QuestionsAnswered'];
    $questionsCorrect+= $resultDetails['QuestionsCorrect'];
}

//Work out total percentage accuracy to 2 decimal places so that it is easy to read
$totalQuestionsAccuracy = number_format(($questionsCorrect/$questionsAnswered)*100, 2) + 0;

//Fetch all topic details
$topicDetails = $connection->getTopics();

//Fetch recent topics completed
$recentTopics = $connection->getRecentTopics($_SESSION['accountID']);

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
                echo '<p class="alert alert-warning"><strong>You are not currently in a class.</strong></br>Join a class to recieve and complete assignments set by your teacher. Ask them for a class code and enter this in the <a class="alert-link" href="/student/profile.php">profile</a> page.</p>';
            }else {
                echo '<table class="table"><thead><tr><th scope="col">Class Name</th><th scope="col">Class Description</th></tr></thead><tbody><tr><td>'.$classDetails['ClassName'].'</td><td>'.$classDetails['ClassDescription'].'</td></tr></tbody></table>';
                echo '<h5 class="mt-2">Due Assignments</h5><table class="table"><thead><tr><th scope="col">Date Due</th><th scope="col">Topic</th><th scope="col">Percentage</th></tr></thead><tbody>';
                foreach($assignmentDetails as $assignment) {
                    echo '<tr><td>';
                    echo $assignment['Date'];
                    echo '</td><td>';
                    echo $assignment['TopicName'];
                    echo '</td>';
                    $result = $connection->getAssignmentResult($assignment['AssignmentID'], $_SESSION['accountID'])->fetch_assoc();
                    if($result === null) {
                        echo '<td><a class="badge badge-danger" href="/student/learn.php?assignment='.$assignment['AssignmentID'].'">Click to attempt</a></td>';
                    }else {
                        $percentage = number_format((($result['QuestionsCorrect']/$result['QuestionsAnswered'])*100), 2);
                        echo '<td><p class="badge badge-primary">'.$percentage.'%</p></td>';
                    }
                }
                echo '</tbody></table>';
            }
            ?>
        </div>
        <div class="card p-4 m-2">
            <!-- Learn section for custom learning -->
            <h5>Learn</h5>
            <p>Learn topics outside of a class-set assignment.</p>
            <btn class="btn btn-small btn-primary mb-4" data-mdb-toggle="modal" data-mdb-target="#learn">Learn</btn>
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
                    <?php
                    //Calculate the strongest and weakest topics and display all this data in a table
                    $max = -1;
                    $strongestTopic = "No data";
                    $min = 101;
                    $weakestTopic = "No data";
                    //Loop through recent topics
                    foreach($recentTopics as $topic) {
                        echo '<tr><td>';
                        echo $topic['DateCompleted'];
                        echo '</td><td>';
                        echo $topic['TopicName'];
                        echo '</td><td>';
                        //Compare percentage against the max and min recorded percentage.
                        $percentage = number_format(($topic['QuestionsCorrect']/$topic['QuestionsAnswered']*100), 2);
                        if($percentage > $max) {
                            $max = $percentage;
                            $strongestTopic = $topic['TopicName'];
                        }
                        if($percentage < $min) {
                            $min = $percentage;
                            $weakestTopic = $topic['TopicName'];
                        }
                        echo $percentage."%";
                        echo '</td></tr>';
                    }
                    //Display max and min percentage
                    echo '<p class="badge badge-success"> Strongest Topic: '.$strongestTopic.'</p>';
                    echo '<p class="badge badge-danger"> Weakest Topic: '.$weakestTopic.'</p>';
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!--Modal form for creating a new class -->
<div class="modal fade" id="learn" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Learn</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input class="form-control" onkeyup="searchTopic()" placeholder="Search for a topic..." type="text" id="topicSearch"></input>
                <p id="searchError"></p>
                <table class="table">
                    <thead>
                            <th scope="col">Topic Name</th>
                            <th scope="col">Topic Description</th>
                            <th scope="col">Topic Author</th>
                            <th scope="col"></th>
                    </thead>
                    <tbody id="topicTable">
                    <?php
                        foreach($topicDetails as $topic) {
                            echo '<tr><td>';
                            echo $topic['TopicName'];
                            echo '</td><td>';
                            echo $topic['TopicDescription'];
                            echo '</td><td>';
                            //Grab the name of an author
                            $authorDetails = $connection->getUserByID($topic['AuthorID'], 'teacher')->fetch_assoc();
                            echo $authorDetails['Username'];
                            echo '</td><td><a class="btn btn-primary" href="/student/learn.php?topic='.$topic['TopicID'].'">Learn</a></td></tr>';
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<?php
require("include/footer.php");
?>

<script>
    //JavaScript
    //This function is called every time an input is taken in a search box. It performs a fuzzy search.
    function searchTopic() {
      var tableData, index, textValue;
      var searchTerm = document.getElementById("topicSearch");
      var searchTermFilter = searchTerm.value.toUpperCase();
      var table = document.getElementById("topicTable");
      var row = table.getElementsByTagName("tr");
      var value = false;
    
      //Traverse through every topic in the table (each as a row)
      for(index = 0; index <= row.length-1; index++) {
        //Make the row visible
        row[index].style.display = "none";
        //This grabs col 0 (the title col for the row)
        tableData = row[index].getElementsByTagName("td")[0];
        //If statement checks to see if we have any topics
        if (tableData) {
          textValue = tableData.textContent || tableData.innerText;
          //Check to see if the search term exists within the title (this is similar to a linear search)
          if (textValue.toUpperCase().indexOf(searchTermFilter) > -1) {
            //Leave the row visible if the search term exists within the title of the topic
            row[index].style.display = "";
            value = true;
          } else {
            row[index].style.display = "none";
          }
        }

        //If a value could not be found
        if (value === false) {
          document.getElementById("searchError").innerHTML = "No topics could be found";
        }else {
          document.getElementById("searchError").innerHTML = "";
        }
      }
    }
</script>