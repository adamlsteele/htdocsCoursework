<?php
require("include/header.php");

//Redirect to login page if there are no authenticated sessions for the student account type
if(!(isset($_SESSION['accountType']) && $_SESSION['accountType'] == 'teacher')) {
    header("Location: /?error=Please authenticate");
}

$connection = new Connection;

//Grab the ID of the assignment that the teacher would like to manage
$assignmentID = $_GET['id'];

$assignmentDetails = $connection->getAssignmentByID($assignmentID)->fetch_assoc();
$studentsInClass = $connection->getStudentsByID($assignmentDetails['ClassID']);
$resultsArray = array();

//Total variable used to sum up all the percentages
$total = 0;
//Variable that stores the total questions attempted
$totalQuestions = 0;
//Variable that stores the total questions that were answered correctly
$totalQuestionsCorrect = 0;
$average;
$students = 0;

//Add a username and percentage score to an array that can be sorted
foreach($studentsInClass as $student) {
    $assignmentResult = $connection->getAssignmentResult($assignmentID, $student['StudentID'])->fetch_assoc();
    $percentage = ($assignmentResult['QuestionsCorrect']/$assignmentResult['QuestionsAnswered'])*100;
    array_push($resultsArray, array($student['Username'], $percentage, $student['StudentID']));
    if(!is_nan($percentage)){
        $total = $total + $percentage;
        $students++;
    }
    $totalQuestions = $totalQuestions + $assignmentResult['QuestionsAnswered'];
    $totalQuestionsCorrect = $totalQuestionsCorrect + $assignmentResult['QuestionsCorrect'];
}

$average = ($totalQuestions / $totalQuestionsCorrect)*100;

//Insertion sort to sort the results

function insertionSort(&$array, $n) {
    //N is the length of the array. Increment until the end of the array.
    //This specific algorithm is sorting the 1st index of the results array (the percentage and not the student name)
    for($i=0; $i<$n; $i++) {
      $j = $i - 1;
      //Second while loop determines the place to place the currently selected index in the newer sorted partition of the array
      while($j >= 0 && $array[$i][1] > $array[$j][1]) {
        $temp = $array[$j + 1];
        $array[$j + 1] = $array[$j];
        $array[$j] = $temp;
        $j = $j - 1;
      }
    }
}

//Call the insertion sort function
insertionSort($resultsArray, count($resultsArray));

?>
<div class="m-1 row justify-content-center">
    <div class="col-lg-8">
        <div class="container text-center">
            <div class="row">
                <div class="card col m-2 p-2">
                    <div class="card-body">
                        <h5 class="card-title">Average Percentage</h5>
                        <h6 class="card-subtitle mb-2 text-muted"><?php echo (($totalQuestionsCorrect/$totalQuestions)*100); ?>%</h6>
                    </div>
                </div>
                <div class="card col m-2 p-2">
                    <div class="card-body">
                        <h5 class="card-title">Total Students Completed</h5>
                        <h6 class="card-subtitle mb-2 text-muted"><?php echo $students; ?></h6>
                    </div>
                </div>
                <div class="card col m-2 p-2">
                    <div class="card-body">
                        <h5 class="card-title">Total Questions Answered</h5>
                        <h6 class="card-subtitle mb-2 text-muted"><?php echo $totalQuestions; ?></h6>
                    </div>
                </div>
                <div class="card col m-2 p-2">
                    <div class="card-body">
                        <h5 class="card-title">Total Questions Correct</h5>
                        <h6 class="card-subtitle mb-2 text-muted"><?php echo $totalQuestionsCorrect; ?></h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 m-2 card">
        <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Student Username</th>
                        <th scope="col">Student Percentange</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody id="assignmentTable">
                    <button class="btn btn-sm btn-primary" id="filterCompleted" onClick="filterCompleted()">Hide uncompleted</button>
                    <?php
                    foreach($resultsArray as $student) {
                        echo '<tr><td>';
                        echo $student[0];
                        echo '</td><td>';
                        if(is_nan($student[1])) {
                            echo '<p class="badge badge-danger">Not attempted</p>';
                        }else {
                            echo $student[1]."%";
                            echo '</td><td>';
                            echo '<button class="btn btn-sm btn-primary" onclick="editScoreLoad('.$student[2].', '.$assignmentID.')" data-mdb-toggle="modal" data-mdb-target="#changeScore">View Answers</a>';
                        }
                        echo '</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
            <a href="/actions/removeAssignment.php?id=<?php echo $assignmentID; ?>&ret=<?php echo $assignmentDetails['ClassID'];?>" class="btn btn-danger btn-block">Remove Assignment</a>
        </div>
    </div>
</div>

<div class="modal fade" id="changeScore" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Edit Score</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="editScoreContainer"></div>
            </div>
        </div>
    </div>
</div>


<?php
require("include/footer.php");
?>
<script>
var toggle = "show";

//Filter that will toggle between showing scores of students that have and have not completed the assignment
function filterCompleted() {
    var table = document.getElementById("assignmentTable");
    var row = table.getElementsByTagName("tr");
    var button = document.getElementById("filterCompleted");
    if(toggle == "show") {
        toggle = "hide";
        //Hiding the scores of students where the percentage is equal to "not attempted"
        for(var index = 0; index <= row.length-1; index++) {
            row[index].style.display = "none";
            var tableData = row[index].getElementsByTagName("td")[1];
            if(tableData.innerText.indexOf("Not attempted") > -1) {
                //Setting the style of a HTML element to none, hides it from view. In this case we are hiding it from the table of results.
                row[index].style.display = "none";
            }else {
                //Do not hide the element if completed.
                row[index].style.display = "";
            }
        }
        button.innerText = "Show uncompleted";
    }else {
        toggle = "show"
        //Show all items, ensure all items do not have the styling restriction
        for(var index = 0; index <= row.length-1; index++) {
            row[index].style.display = "";
        }
        button.innerText = "Hide uncompleted";
    }
}

//Dynamically loads in the form to update a user score every time the popup is clicked
function editScoreLoad(studentID, assignmentID) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("editScoreContainer").innerHTML = this.responseText;
        }
    };
    xmlhttp.open("GET", "/actions/editScore.php?id="+studentID+"&a="+assignmentID, true);
    xmlhttp.send();
}

</script>

