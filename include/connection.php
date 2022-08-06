<?php
class Connection {
    //First calls when an object is made based on the class
    //Sets up a connection using a connection string
    private $connection;
    private $query;

    public function __construct() {
        //Variables used to form a connection string
        $host = "localhost";
        $username = "root";
        $database = "cloud_coding";

        //Establish a connection and return the connection error if failed
        $this->connection = new mysqli($host, $username, "", $database);
        if($this->connection->connect_error) {
            die("Connection was not established: " . $this->connection->connect_error);
        }
    }

    //Get account details from email
    public function getUserByEmail(string $email, $accountType) {
        $this->query = "SELECT * FROM ".$accountType." WHERE Email = '".$email."'";
        return $this->connection->query($this->query);
    }

    //Get account details from id
    public function getUserByID(int $id, $accountType) {
        if($accountType == "student") {
            $this->query = "SELECT * FROM student WHERE StudentID = ".$id;
        }else {
            $this->query = "SELECT * FROM teacher WHERE TeacherID = ".$id;
        }

        return $this->connection->query($this->query);
    }

    //Get class details from id
    public function getClassByID(int $id) {
        $this->query = "SELECT * FROM class WHERE ClassID = ".$id;
        return $this->connection->query($this->query);
    }

    //Create an account of certain type for a user and then return the account details
    public function createAccount($email, $username, $password, $accountType) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO ".$accountType."(Email, Username, Password) VALUES('".$email."', '".$email."', '".$hashedPassword."')";

        $this->connection->query($query);

        //Return the created account details via a GET statement
        return $this->getUserByEmail($email, $accountType);
    }

    public function getAssignmentsByClassID($id) {
        $this->query = "SELECT 'Date', 'topic.Topic Name' FROM assignment INNER JOIN topic on assignment.TopicID = topic.TopicID WHERE ClassID = ".$id." AND Date > CURRENT_DATE()";
        echo $this->query;
        return $this->connection->query($this->query);
    }
}

?>