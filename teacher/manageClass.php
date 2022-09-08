<?php
require("include/header.php");

//Redirect to login page if there are no authenticated sessions for the student account type
if(!(isset($_SESSION['accountType']) && $_SESSION['accountType'] == 'teacher')) {
    header("Location: /?error=Please authenticate");
}

//Grab the ID of the class that the teacher would like to manage
$classID = $_GET['id'];

$connection = new Connection;
//Grab details of the class
$classDetails = $connection->getClassByID($classID)->fetch_assoc();
//Grab details of the students within a class
$studentsInClass = $connection->getStudentsByID($classID);
//Grab details of all assignments
$assignments = $connection->getAssignmentsByClassID($classID);
?>

<div class="m-1 row justify-content-center">
    <div class="col-lg-8">
        <div class="p-4 m-2 card">
            <h3>Manage Class</h3>
            <form action="/actions/editClass.php?ret=<?php echo $classID;?>" method="POST">
                <input value="<?php echo $classID;?>" name="classID" hidden></input>
                <div class="form-outline mb-4">
                    <input class="form-control" id="name" maxlength=64 required type="text" name="name" value="<?php echo $classDetails['ClassName']; ?>"/>
                    <label class="form-label" for="name" >Class Name</label>
                </div>
                <div class="form-outline mb-4">
                    <input class="form-control" id="descriptiob" required type="text" name="description" value="<?php echo $classDetails['ClassDescription']; ?>"/>
                    <label class="form-label" for="description" >Class Description</label>
                </div>
                <button class="btn btn-block btn-primary mb-1" action="submit">Save Changes</button>
                <a class="btn btn-block btn-danger" onclick="return confirm('Are you sure you want to delete this class? This change is irreversible. Click ok to continue.');"href="/actions/deleteClass.php?id=<?php echo $classID; ?>">Delete Class</a>
            </form>
            </br>
        </div>
        <div class="p-4 m-2 card">
            <h5>Students</h5>
            <p class="badge badge-warning">Class Code: <?php echo $classDetails['ClassCode'];?></p>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Student Username</th>
                        <th scope="col">Student Email</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($studentsInClass as $student) {
                        echo '<tr><td>';
                        echo $student['Username'];
                        echo '</td><td>';
                        echo $student['Email'];
                        echo '</td><td>';
                        echo '<a class="text-danger" onclick="return confirm('."'Are you sure you want to remove this student? This change is irreversible. Click ok to continue.'".');" href="/actions/removeStudent.php?id='.$student['StudentID'].'&ret='.$classID.'">Remove From Class</a></br>';
                        echo '</td></tr>';
                    }
                    ?>
                </tbody>
            </table>       
            </br>
            <h5>Assignments</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Due Date</th>
                        <th scope="col">Topic Name</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    foreach($assignments as $assignment) {
                        echo '<tr><td>';
                        echo $assignment['Date'];
                        echo '</td><td>';
                        echo $assignment['TopicName'];
                        echo '</td><td>';
                        echo '<a class="text-primary" href="/teacher/manageAssignment.php?id='.$assignment['AssignmentID'].'">Manage Assignment</a>';
                        echo '</td></tr>';
                    }
                ?>
                </tbody>
            </table>
            <button class="btn btn-primary" onclick="loadPastAssignments(<?php echo $classID;?>)">Load Past Assignments</button>
            <div id="pastAssignmentsContainer"></div>
    </div>
</div>

<script>
    //Function to actively get past assignments when the button is clicked
    function loadPastAssignments(id) {
        //Performs a HTTP Request to the past /actions/loadPastAssignments
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("pastAssignmentsContainer").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "/actions/loadPastAssignments.php?id="+id, true);
        xmlhttp.send();
    }
</script>


<?php
require("include/footer.php");
?>