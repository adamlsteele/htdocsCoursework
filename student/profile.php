<?php
require("include/header.php");

//Redirect to login page if there are no authenticated sessions for the student account type
if(!(isset($_SESSION['accountType']) && $_SESSION['accountType'] == 'student')) {
    header("Location: /?error=Please authenticate");
}

//Initialise a new connection class
$connection = new Connection;
$userDetails = $connection->getUserByID($_SESSION['accountID'], 'student')->fetch_assoc();
?>


<div class="m-1 row justify-content-center">
    <div class="col-lg-8">
        <?php
        if(isset($_GET['error'])) {
            //Display error validation if an error occurs
            echo '<p class="alert alert-danger">'.$_GET['error'].'</p>';
        }
        ?>
        <div class="p-4 m-2 card">
            <h3>Edit Profile</h3>
            <h5>Your Details</h5>
            <!-- User details -->
            <form action="/actions/editProfile.php" method="post">
                <div class="form-outline mb-4">
                    <!-- Email Field That Automatically Populates -->
                    <input class="form-control" id="email" maxlength=64 required type="text" name="email" disabled value="<?php echo $userDetails['Email']; ?>"/>
                    <label class="form-label" for="email" >Email</label>
                </div>
                <div class="form-outline mb-4">
                    <!-- Username Field That Automatically Populates -->
                    <input class="form-control" id="username" maxlength=64 required type="text" name="username" value="<?php echo $userDetails['Username']; ?>"/>
                    <label class="form-label" for="username" >Username</label>
                </div>
                <div class="form-outline mb-2">
                    <!-- Password Field To Enter New Password -->
                    <input class="form-control" id="newPassword" maxlength=32 type="password" name="newPassword" value=""/>
                    <label class="form-label" for="newPassword" >New Password</label>
                </div>
                <div class="form-outline mt-2 mb-4">
                    <!-- Password Verification -->
                    <input class="form-control" id="confirmNewPassword" maxlength=32 type="password" name="confirmNewPassword" value=""/>
                    <label class="form-label" for="confirmNewPassword" >Confirm New Password</label>
                </div>
                <div class="form-outline mt-2 mb-4">
                    <!-- Enter Current Password Field -->
                    <input class="form-control" id="currentPassword" maxlength=32 required type="password" name="password" value=""/>
                    <label class="form-label" for="currentPassword" >Current Password</label>
                </div>
                <button class="btn btn-block btn-primary" type="submit">Save changes</button>
            </form>
        </div>
        <div class="p-4 m-2 card">
            <h5>Your Class</h5>
            <!-- Class details -->
            <?php if($userDetails['ClassID'] === null) {
                //If a user is not in a class the page displays an input box to enter a code
                echo '<p class="alert alert-primary">Enter the six digit code from your teacher below</p>';
                echo '<form action="/actions/joinClass.php" method="post">';
                echo '<div class="form-outline mb-4">';
                echo '<input class="form-control" type="text" maxlength=6 minlength=6 name="code" required/>';
                echo '<label class="form-label">Class Code</label>';
                echo '</div>';
                echo '<button class="btn btn-block btn-primary" type="submit">Join Class</button>';
                echo '</form>';
            }else{
                //If a user is in a class they are provided with the class name and the ability to leave a class
                $classDetails = $connection->getClassByID($userDetails['ClassID'])->fetch_assoc();
                echo '<table class="table"><thead><tr><th scope="col">Class Name</th><th scope="col">Class Description</th></tr></thead><tbody><tr><td>'.$classDetails['ClassName'].'</td><td>'.$classDetails['ClassDescription'].'</td></tr></tbody></table>';
                echo '<a class="btn btn-danger" href="/actions/leaveClass.php?id='.$_SESSION['accountID'].'">Leave Class</a>';
            }
            ?>
        </div>
    </div>
</div>

<?php
require("include/footer.php");
?>