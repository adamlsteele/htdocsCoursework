<?php
require("include/header.php");

//Redirect to login page if there are no authenticated sessions for the student account type
if(!(isset($_SESSION['accountType']) && $_SESSION['accountType'] == 'teacher')) {
    header("Location: /?error=Please authenticate");
}


//Grab account details for ID stored within session variable
$connection = new Connection();
$userDetails = $connection->getUserByID($_SESSION['accountID'], "teacher")->fetch_assoc();

$username = $userDetails['Username'];

//Grab classes that a teacher is in
$classes = $connection->getClassesByTeacherID($_SESSION['accountID']);

//Grab all possible topics that can be completed as assignments
$topics = $connection->getTopics();
?>

<div class="m-1 row justify-content-center">
    <div class="col-lg-8">
        <div class="p-4 m-2 card">
            <!-- Welcome message -->
            <h3>Hello, <?php echo $username; ?></h3>
            <h5>Teacher Dashboard</h5>
            <p>Manage your classes here.</p>
        </div>
        <div class="p-4 m-2">
            <h5>Your Classes</h5>
            <btn class="btn btn-small btn-primary mb-4" data-mdb-toggle="modal" data-mdb-target="#newClass">New Class</btn>
            <a class="btn btn-small btn-primary mb-4" data-mdb-toggle="modal" data-mdb-target="#newAssignment">New Assignment</a>
            <div class="row row-cols-1 row-cols-md-3">
                <?php
                if($classes->num_rows === 0) {
                    echo '<p class="alert alert-warning">Click new class to create and manage a new class.</p>';
                }else{
                    foreach($classes as $class) {
                        echo '<div class="col h-100 card p-4 m-2 border border-'.$class['ClassColour'].'" style="max-width: 18rem;">';
                        echo '<h3>'.$class['ClassName'].'</h3>';
                        echo '<p>'.$class['ClassDescription'].'</p>';
                        echo '<p><strong class="badge badge-'.$class['ClassColour'].'">'.$class['ClassCode'].'</strong></p>';
                        echo '<a class="btn btn-'.$class['ClassColour'].'" href="/teacher/manageClass.php?id='.$class["ClassID"].'">Manage Class</a></div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!--Modal form for creating a new class -->
<div class="modal fade" id="newAssignment" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>New Assignment</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="newAssignment" action="/actions/newAssignment.php" method="post">
                    <div class="form-outline mb-4">
                        <label class="form-label" for="class" >Select Class</label>
                        <select id="class" name="class" class="form-select">
                            <?php
                            foreach($classes as $class) {
                                echo '<option value="'.$class['ClassID'].'">'.$class['ClassName'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-outline mb-4">
                        <label class="form-label" for="class" >Select Topic</label>
                        <select id="topicList" class="form-control" name="topic">
                        
                            <?php
                            foreach($topics as $topic) {
                                echo '<option value="'.$topic['TopicID'].'">'.$topic['TopicName'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-outline mb-4 datepicker date" data-provide="datepicker">
                        <label class="form-label" for="date" >Select Due Date</label>
                        <input type="date" class="form-control" name="dueDate" id="date" placeholder="YYYY/MM/DD">
                    </div>

                    <button class="btn btn-primary btn-lg btn-block mt-4" type="submit">New assignment</button>
                </form>
            </div>
        </div>
    </div>
</div>
        

<!--Modal form for creating a new class -->
<div class="modal fade" id="newClass" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>New Class</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="/actions/newClass.php" method="post">
                    <div class="form-outline mb-4">
                        <input class="form-control" id="name" maxlength=64 required type="text" name="name"/>
                        <label class="form-label" for="name" >Class Name</label>
                    </div>
                    <div class="form-outline mb-4">
                        <input class="form-control" id="description" maxlength=64 required type="text" name="description"/>
                        <label class="form-label" for="description" >Class Description</label>
                    </div>
                    <p>Select class colour</p>
                    <div class="custom-control custom-radio">
                        <input value="success" type="radio" class="custom-control-input mb-2" id="classColourGreen" name="classColour" required>
                        <label class="custom-control-label badge badge-success" for="classColourGreen">Green</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input value="primary" type="radio" class="custom-control-input mb-2" id="classColourBlue" name="classColour" required>
                        <label class="custom-control-label badge badge-primary" for="classColourBlue">Blue</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input value="info" type="radio" class="custom-control-input mb-2" id="classColourAqua" name="classColour" required>
                        <label class="custom-control-label badge-info badge" for="classColourAqua">Aqua</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input value="warning" type="radio" class="custom-control-input mb-2" id="classColourOrange" name="classColour" required>
                        <label class="custom-control-label badge badge-warning" for="classColourOrange">Orange</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input value="danger" type="radio" class="custom-control-input mb-2" id="classColourRed" name="classColour" required>
                        <label class="custom-control-label badge badge-danger" for="classColourRed">Red</label>
                    </div>
                    <button class="btn btn-primary btn-lg btn-block mt-4" type="submit">New class</button>
            </div>
</div>


<?php
require("include/footer.php");
?>
