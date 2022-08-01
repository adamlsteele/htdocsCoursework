<?php
require("include/header.php");
?>

<div class="m-1 row justify-content-center">
    <div class="col-lg-6">
        <div class="card p-4 m-2">
            <h3>Hello, </h3>
            <h5>Student Dashboard</h5>
            <p>Access assignments, view progress and start your own revision sessions here.</p>
        </div>
        <div class="card p-4 m-2">
            <h5>Your Class</h5>
            <p class="alert alert-warning"><strong>You are not currently in a class.</strong></br>Join a class to recieve and complete assignments set by your teacher. Ask them for a class code and click the button below.</p>
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
                    <div class="col m-2"><h5>0</h5> <span class="p-2">Questions Answered</span></div>
                    <div class="col m-2"><h5>0</h5> <span class="p-2">Questions Correct</span></div>
                    <div class="col m-2"><h5>0</h5> <span class="p-2">Question Accuracy</span></div>
                </div>
            </div>

            <h5>Recent Sessions</h5>
        </div>
    </div>
</div>

<?php
require("include/footer.php");
?>