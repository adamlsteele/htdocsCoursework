<?php
require "include/header.php";

//Redirect the user to the appropiate account dashboard if they already have an active session
if(isset($_SESSION['accountType'])) {
    header("Location: /".$_SESSION['accountType']);
}

?>

<div class="d-flex justify-content-center m-4">
    <div class="col-md-4">
        <!-- Sign In Card -->
        <div class="card p-4">
            <div class="card-body">        
                <!-- Sign In Card Title -->
                <h3 class="text-center">Cloud Coding</h3>
                <h5 class="text-center">Sign In</h5>

                <!-- Account Type Selector -->
                <ul class="nav nav-pills justify-content-center">
                    <li class="nav-item text-center">
                        <button class="nav-link active" id="type-student-tab" data-bs-toggle="pill" data-bs-target="#type-student" type="button" role="tab">Student</button>
                    </li>
                    <li class="nav-item text-center">
                        <button class="nav-link" id="type-teacher-tab" data-bs-toggle="pill" data-bs-target="#type-teacher" type="button" role="tab">Teacher</button>
                    </li>
                </ul>

                <?php
                //Show error if authentication failed
                if(isset($_GET['error'])) {
                    echo '<div class="alert alert-danger mt-4">'.$_GET['error'].'</div>';
                }
                ?>
                
                <!-- Student Sign In Form -->
                <div class="tab-content" id="accTypeContent">
                    <div class="tab-pane fade show active justify-content-center" id="type-student" role="tab-panel">
                        <form action="/actions/auth.php?type=student" method="post">
                            <h5 class="mt-4">Student</h5>
                            <div class="form-outline mb-4">
                                <input class="form-control" id="email" maxlength=64 required type="email" name="email"/>
                                <label class="form-label" for="email" >Email</label>
                            </div>
                            <div class="form-outline mb-4">
                                <input class="form-control" id="password" maxlength=32 required type="password" name="password"/>
                                <label class="form-label" for="password" >Password</label>
                            </div>
                            <button class="btn btn-primary btn-lg btn-block" type="submit">Sign In</button>
                        </form>
                    </div>

                    <!-- Teacher Sign In Form -->
                    <div class="tab-pane fade" id="type-teacher" role="tab-panel">
                        <form action="/actions/auth.php?type=teacher" method="post">
                            <h5 class="mt-4">Teacher</h5>
                            <div class="form-outline mb-4">
                                <input class="form-control" id="email" maxlength=64 required type="email" name="email"/>
                                <label class="form-label" for="email" >Email</label>
                            </div>
                            <div class="form-outline mb-4">
                                <input class="form-control" id="password" maxlength=32 required type="password" name="password"/>
                                <label class="form-label" for="password" >Password</label>
                            </div>
                            <button class="btn btn-primary btn-lg btn-block" type="submit">Sign In</button>
                        </form>
                    </div>
                </div>

                <!-- Sign In Link -->
                <p>Need an account? Click  <a href="/signup.php">here</a>.</p>

            </div>
        </div>
    </div>
</div>

<?php
require "./include/footer.php";
?>