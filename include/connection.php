<?php
class Connection {
    //First calls when an object is made based on the class
    //Sets up a connection using a connection string
    private $connection;
    private $query;

    public function __construct() {
        //Variables used to form a connection string
        $host = "localhost";
        $username = "miniecrb_cloudcoding";
        $database = "miniecrb_cloudcoding";
        $password = "miniecrb_cloudcoding";

        //Establish a connection and return the connection error if failed
        $this->connection = new mysqli($host, $username, $password, $database);
        if($this->connection->connect_error) {
            die("Connection was not established: " . $this->connection->connect_error);
        }
    }

    //General update query that does not return any data
    public function query(string $query) {
        return $this->connection->query($query);
    }

    public function getTopicByID(int $id) {
        $this->query = "SELECT * FROM topic WHERE TopicID =".$id;
        return $this->connection->query($this->query);
    }

    public function getAssignmentByID(int $id) {
        $this->query = "SELECT * FROM assignment WHERE AssignmentID =".$id;
        return $this->connection->query($this->query);
    }

    public function getQuestionsByID(int $topicID) {
        $this->query = "SELECT * FROM question WHERE TopicID =".$topicID;
        return $this->connection->query($this->query);
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

    //Get student details within a class
    public function getStudentsByID(int $id) {
        $this->query = "SELECT * FROM Student WHERE ClassID = ".$id;
        return $this->connection->query($this->query);
    }

    //Get class details from code
    public function getClassByCode(string $code) {
        $this->query = "SELECT * FROM class WHERE ClassCode = '".$code."'";
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

    public function getQuestionByID($id) {
        $this->query = "SELECT * FROM Question WHERE QuestionID = ".$id;
        return $this->connection->query($this->query);
    }

    public function createAssignment($class, $topic, $dueDate) {
        $this->query = "INSERT INTO Assignment(ClassID, TopicID, Date) VALUES(".$class.", ".$topic.", '".$dueDate."')";
        return $this->connection->query($this->query);
    }

    public function getAssignmentResult($assignmentID, $studentID) {
        $this->query = "SELECT * FROM Result WHERE AssignmentID = ".$assignmentID." AND StudentID = ".$studentID;
        return $this->connection->query($this->query);
    }

    public function getRecentTopics($id) {
        $this->query = "SELECT * FROM result INNER JOIN topic ON result.TopicID = topic.TopicID WHERE StudentID = ".$id." ORDER BY DateCompleted ASC LIMIT 2";
        return $this->connection->query($this->query);
    }

    public function getResultsbyID($id) {
        $this->query = "SELECT * FROM result WHERE StudentID = ".$id;
        return $this->connection->query($this->query);
    }

    public function getPastAssignmentsByID($id) {
        $this->query = "SELECT AssignmentID, Date, topic.TopicName FROM assignment INNER JOIN topic on assignment.TopicID = topic.TopicID WHERE ClassID = ".$id." AND Date < CURRENT_DATE()";
        return $this->connection->query($this->query);
    }

    public function deleteClass($id) {
        //Update all the students so that they are no longer in a class
        $this->query = "SELECT * FROM Student WHERE ClassID = ".$id;
        $students = $this->connection->query($this->query);
        foreach($students as $student) {
            $this->query = "UPDATE Student
            SET ClassID = null
            WHERE StudentID = ".$student['StudentID'];
            $this->connection->query($this->query);
        }

        //Delete the class from record
        $this->query = "DELETE FROM Class WHERE ClassID = ".$id;
        return $this->connection->query($this->query);
    }

    public function addTopicResult($topicID, $studentID, $questionsAnswered, $questionsCorrect, $questionOne, $questionOneResult, $questionTwo, $questionTwoResult, $questionThree, $questionThreeResult, $questionFour, $questionFourResult, $questionFive, $questionFiveResult) {
        $this->query = "INSERT INTO result(TopicID, StudentID, QuestionsAnswered, QuestionsCorrect, QuestionOneID, QuestionOneAnswer, QuestionTwoID, QuestionTwoAnswer,  QuestionThreeID, QuestionThreeAnswer, QuestionFourID, QuestionFourAnswer, QuestionFiveID, QuestionFiveAnswer) 
        VALUES (".$topicID.", ".$studentID.", ".$questionsAnswered.", ".$questionsCorrect.", ".$questionOne.", '".$questionOneResult."', ".$questionTwo.", '".$questionTwoResult."', ".$questionThree.", '".$questionThreeResult."', ".$questionFour.", '".$questionFourResult."', ".$questionFive.", '".$questionFiveResult."')";
        return $this->connection->query($this->query);
    }

    public function addAssignmentResult($assignmentID, $topicID, $studentID, $questionsAnswered, $questionsCorrect, $questionOne, $questionOneResult, $questionTwo, $questionTwoResult, $questionThree, $questionThreeResult, $questionFour, $questionFourResult, $questionFive, $questionFiveResult) {
        $this->query = "INSERT INTO result(AssignmentID, TopicID, StudentID, QuestionsAnswered, QuestionsCorrect, QuestionOneID, QuestionOneAnswer, QuestionTwoID, QuestionTwoAnswer,  QuestionThreeID, QuestionThreeAnswer, QuestionFourID, QuestionFourAnswer, QuestionFiveID, QuestionFiveAnswer) 
        VALUES (".$assignmentID.", ".$topicID.", ".$studentID.", ".$questionsAnswered.", ".$questionsCorrect.", ".$questionOne.", '".$questionOneResult."', ".$questionTwo.", '".$questionTwoResult."', ".$questionThree.", '".$questionThreeResult."', ".$questionFour.", '".$questionFourResult."', ".$questionFive.", '".$questionFiveResult."')";
        $this->connection->query($this->query);
        return;
    }

}

?>