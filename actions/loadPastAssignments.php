<?php
//Include connection file
session_start();
require "include/connection.php";

//Initialise a new connection
$connection = new Connection;

//Fetch the past assignments using a class ID.
$classID = $_GET['id'];
$assignments = $connection->getPastAssignmentsByID($classID)

//Echo past assignments within a table
?>
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
