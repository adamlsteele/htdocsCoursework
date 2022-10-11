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
    array_push($resultsArray, array($student['Username'], $percentage, $student['StudentID']));
}

function insertionSort(&$array, $n) {
    for($i=0; $i<$n; $i++) {
      $j = $i - 1;
      while($j >= 0 && $array[$i][1] > $array[$j][1]) {
        $temp = $array[$j + 1];
        $array[$j + 1] = $array[$j];
        $array[$j] = $temp;
        $j = $j - 1;
      }
    }
}
insertionSort($resultsArray, count($resultsArray));
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
                    foreach($resultsArray as $student) {
                        echo '<tr><td>';
                        echo $student[0];
                        echo '</td><td>';
                        if($student[1] === null) {
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

