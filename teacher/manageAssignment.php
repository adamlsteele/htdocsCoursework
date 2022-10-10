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

//Add a username and percentage score to an array that can be sorted
foreach($studentsInClass as $student) {
    $assignmentResult = $connection->getAssignmentResult($assignmentID, $student['StudentID'])->fetch_assoc();
    $percentage = ($assignmentResult['QuestionsCorrect']/$assignmentResult['QuestionsAnswered'])*100;
    array_push($resultsArray, array($student['Username'], $percentage));
}

function sortArray(&$arrayToSort, $low, $high) {
    //Sort a particular index so that it is in its correct position
    $partitionIndex = partitionArray($arrayToSort, $low, $high);
    if($low < $index-1) {
        sortArray($arrayToSort, $low, $index-1);
    }else if($index < $high) {
        sortArray($arrayToSort, $index, $high);
    }
}

function partitionArray(&$arrayToSort, $low, $high) {
    $pivotPoint = $arrayToSort[($low + $high)/2][1];
    while ($low <= $high) {
        while ($arrayToSort[$low][1] < $pivotPoint){
            $low++;
        }
        while ($arrayToSort[$high][1] > $pivotPoint){
            $high--;
        }
        if($low <= $high) {
            $temp = $arrayToSort[$low];
            $arrayToSort[$low] = arrayToSort[$high];
            $arrayToSort[$high] = $temp;
            $low++;
            $high--;
        }
    }
    return $low;
}

sortArray($resultsArray);

echo print_r($resultsArray);


?>
<div class="m-1 row justify-content-center">
    <div class="col-lg-8">
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
                    foreach($studentsInClass as $student) {
                        echo '<tr><td>';
                        echo $student['Username'];
                        echo '</td><td>';
                        $assignmentResult = $connection->getAssignmentResult($assignmentID, $student['StudentID'])->fetch_assoc();
                        if($assignmentResult['QuestionsCorrect'] === null) {
                            echo '<p class="badge badge-danger">Not attempted</p>';
                        }else {
                            echo ($assignmentResult['QuestionsCorrect']/$assignmentResult['QuestionsAnswered'])*100 . "%";
                            echo '</td><td>';
                            echo '<button class="btn btn-sm btn-primary" onclick="editScoreLoad('.$student['StudentID'].', '.$assignmentID.')" data-mdb-toggle="modal" data-mdb-target="#changeScore">View Answers</a>';
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

function filterCompleted() {
    var table = document.getElementById("assignmentTable");
    var row = table.getElementsByTagName("tr");
    var button = document.getElementById("filterCompleted");
    if(toggle == "show") {
        toggle = "hide";
        for(var index = 0; index <= row.length-1; index++) {
            row[index].style.display = "none";
            var tableData = row[index].getElementsByTagName("td")[1];
            if(tableData.innerText.indexOf("Not attempted") > -1) {
                row[index].style.display = "none";
            }else {
                row[index].style.display = "";
            }
        }
        button.innerText = "Show uncompleted";
    }else {
        toggle = "show"
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

