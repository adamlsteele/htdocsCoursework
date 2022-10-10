<?php
require("include/header.php");
//Destroy the session (nullify the global variables)
session_destroy();
//Redirect back to the login page
header("Location: /");
?>