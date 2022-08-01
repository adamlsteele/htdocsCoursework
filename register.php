<?php
require "include/header.php";

//Redirect the user to the appropiate account dashboard if they already have an active session
if(isset($_SESSION['accountType'])) {
    header("Location: /".$_SESSION['accountType']);
}

?>

<div class="d-flex justify-content-center m-4">
    <div class="col-md-4">
        <div class="card p-4">
            <div class="card-body">
                <h3 class="text-center">Cloud Coding</h3>
                <h5 class="text-center mb-4">Registration</h5>

                <?php
                //Show error if authentication failed
                if(isset($_GET['error'])) {
                    echo '<div class="alert alert-danger mt-4">'.$_GET['error'].'</div>';
                }
                ?>

                <form action="actions/register.php" method="POST">
                    <div class="form-outline">
                        <input class="form-control mb-4" id="username" type="text" name="username" maxlength=64 required />
                        <label class="form-label" for="username">Username</label>
                    </div>
                    <div class="form-outline">
                        <input class="form-control mb-4" id="email" type="email" name="email" maxlength=64 required />
                        <label class="form-label" for="email">Email</label>
                    </div>
                    <div class="form-outline">
                        <input class="form-control mb-2" id="password" type="password" name="password" maxlength=32 required />
                        <label class="form-label" for="password">Password</label>
                    </div>
                    <div class="form-outline">
                        <input class="form-control mb-4" id="confirmPassword" type="password" name="confirmPassword" maxlength=32 required />
                        <label class="form-label" for="confirmPassword">Confirm Password</label>
                    </div>
                    <p>Select account type</p>
                    <div class="custom-control custom-radio">
                        <input value="Student" type="radio" class="custom-control-input" id="studentType" name="accountType" required>
                        <label class="custom-control-label" for="studentType">Student</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input value="Teacher" type="radio" class="custom-control-input mb-4" id="teacherType" name="accountType" required>
                        <label class="custom-control-label" for="teacherType">Teacher</label>
                    </div>
                    <button class="btn btn-primary btn-lg btn-block" type="submit">Register</button>
                </form>
                                    
                <p>Have an account? Click <a href="/">here</a>.</p>
            </div>
        </div>
    </div>
</div>

<?php
require "./include/footer.php";
?>