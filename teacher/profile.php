<?php
require("include/header.php");

//Redirect to login page if there are no authenticated sessions for the student account type
if(!(isset($_SESSION['accountType']) && $_SESSION['accountType'] == 'teacher')) {
    header("Location: /?error=Please authenticate");
}

//Grab the current user details to populate the fields that require them
$connection = new Connection;
$userDetails = $connection->getUserByID($_SESSION['accountID'], 'teacher')->fetch_assoc();
?>


<div class="m-1 row justify-content-center">
    <div class="col-lg-8">
        <?php
        if(isset($_GET['error'])) {
            echo '<p class="alert alert-danger">'.$_GET['error'].'</p>';
        }
        if(isset($_GET['success'])) {
            //Display error validation if an error occurs
            echo '<p class="alert alert-success">'.$_GET['success'].'</p>';
        }
        ?>
        <div class="p-4 m-2 card">
            <h3>Edit Profile</h3>
            <h5>Your Details</h5>
            <form action="/actions/editProfile.php" method="post">
                <div class="form-outline mb-4">
                    <input class="form-control" id="email" maxlength=64 required type="text" name="email" disabled value="<?php echo $userDetails['Email']; ?>"/>
                    <label class="form-label" for="email" >Email</label>
                </div>
                <div class="form-outline mb-4">
                    <input class="form-control" id="username" maxlength=64 required type="text" name="username" value="<?php echo $userDetails['Username']; ?>"/>
                    <label class="form-label" for="username" >Username</label>
                </div>
                <div class="form-outline mb-2">
                    <input class="form-control" id="newPassword" maxlength=32 type="password" name="newPassword" value=""/>
                    <label class="form-label" for="newPassword" >New Password</label>
                </div>
                <div class="form-outline mt-2 mb-4">
                    <input class="form-control" id="confirmNewPassword" maxlength=32 type="password" name="confirmNewPassword" value=""/>
                    <label class="form-label" for="confirmNewPassword" >Confirm New Password</label>
                </div>
                <div class="form-outline mt-2 mb-4">
                    <input class="form-control" id="currentPassword" maxlength=32 required type="password" name="password" value=""/>
                    <label class="form-label" for="currentPassword" >Current Password</label>
                </div>
                <button class="btn btn-block btn-primary" type="submit">Save changes</button>
            </form>
        </div>
    </div>
</div>

<?php
require("include/footer.php");
?>