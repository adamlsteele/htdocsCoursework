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

    //General update query that does not return any data
    public function updateQuery(string $query) {
        return $this->connection->query($query);
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
        $this->query = "SELECT AssignmentID, Date, topic.TopicName FROM assignment INNER JOIN topic on assignment.TopicID = topic.TopicID WHERE ClassID = ".$id." AND Date > CURRENT_DATE()";
        //echo $this->query;
        return $this->connection->query($this->query);
    }

    public function getClassesByTeacherID($id) {
        $this->query = "SELECT * FROM Class WHERE TeacherID = ".$id;
        return $this->connection->query($this->query);
    }

    public function getClassByName($name) {
        $this->query = "SELECT * FROM Class WHERE 'Class Name' = '".$name."'";
        return $this->connection->query($this->query);
    }

    public function createClass($id, $name, $description, $colour, $code) {
        $this->query = "INSERT INTO class(TeacherID, ClassName, ClassDescription, ClassCode, ClassColour) VALUES(".$id.", '".$name."', '".$description."', '".$code."', '".$colour."')";
        echo $this->query;
        return $this->connection->query($this->query);
    }

    public function getTopics() {
        $this->query = "SELECT * FROM Topic";
        return $this->connection->query($this->query);
    }

    public function createAssignment($class, $topic, $dueDate) {
        $this->query = "INSERT INTO Assignment(ClassID, TopicID, Date) VALUES(".$class.", ".$topic.", '".$dueDate."')";
        return $this->connection->query($this->query);
    }

    public function getAssignmentResult($assignmentID, $studentID) {
        $this->query = "SELECT QuestionsAnswered, QuestionsCorrect FROM Result WHERE AssignmentID = ".$assignmentID." AND StudentID = ".$studentID;
        return $this->connection->query($this->query);
    }

    public function getRecentTopics($id) {
        $this->query = "SELECT * FROM result INNER JOIN topic ON result.TopicID = topic.TopicID WHERE StudentID = ".$id." ORDER BY DateCompleted ASC LIMIT 10";
        return $this->connection->query($this->query);
    }
}

?>