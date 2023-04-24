<?php
//#### Connection Class ####
//All SQL queries are handled in this class
//Global class that all other files have access too


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
        $password = "cloudcoding";

        //Establish a connection and return the connection error if failed
        $this->connection = new mysqli($host, $username, $password, $database);
        if($this->connection->connect_error) {
            die("Connection was not established: " . $this->connection->connect_error);
        }
    }

    //General query function that allows for a custom query to be passed through rather than a pre-defined one
    public function query(string $query) {
        $result = $this->connection->query($query);
        if (!$result) {
            die('Error: ' . mysqli_error($this->connection));
        }
        return $result;
    }

    //#### User Related Queries ####
    //
    //
    //Fetches a user's account details via their email
    public function getUserByEmail(string $email, $accountType) {
        $this->query = "SELECT * FROM `".$accountType."` WHERE `Email` = '".$email."'";
        return $this->connection->query($this->query);
    }
    //Fetches a user's account details via their ID
    public function getUserByID(int $id, $accountType) {
        if($accountType == "student") {
            $this->query = "SELECT * FROM `student` WHERE `StudentID` = ".$id;
        }else {
            $this->query = "SELECT * FROM `teacher` WHERE `TeacherID` = ".$id;
        }

        return $this->connection->query($this->query);
    }
    //Create an account of a specific type using the specified parameters
    public function createAccount($email, $username, $password, $accountType) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO `".$accountType."`(Email, Username, Password) VALUES('".$email."', '".$username."', '".$hashedPassword."')";

        $this->connection->query($query);

        //Return the created account details via a GET statement
        return $this->getUserByEmail($email, $accountType);
    }

    //#### Class Related Queries ####
    //
    //
    //Fetches a specific class via its ID
    public function getClassByID(int $id) {
        $this->query = "SELECT * FROM `class` WHERE `ClassID` = ".$id;
        return $this->connection->query($this->query);
    }
    //Fetch all the students in a class via a class ID
    public function getStudentsByID(int $id) {
        $this->query = "SELECT * FROM `student` WHERE `ClassID` = ".$id;
        return $this->connection->query($this->query);
    }
    //Fetch a class via its code
    public function getClassByCode(string $code) {
        $this->query = "SELECT * FROM `class` WHERE `ClassCode` = '".$code."'";
        return $this->connection->query($this->query);
    }
    //Fetch the set of classes that a teacher owns
    public function getClassesByTeacherID($id) {
        $this->query = "SELECT * FROM `class` WHERE `TeacherID` = ".$id;
        return $this->connection->query($this->query);
    }
    //Fetch a class via its name
    public function getClassByName($name) {
        $this->query = "SELECT * FROM `class` WHERE `Class Name` = '".$name."'";
        return $this->connection->query($this->query);
    }
    //Create a class using specified parameters
    public function createClass($id, $name, $description, $colour, $code) {
        $this->query = "INSERT INTO `class`(`TeacherID`, `ClassName`, `ClassDescription`, `ClassCode`, `ClassColour`) VALUES(".$id.", '".$name."', '".$description."', '".$code."', '".$colour."')";
        echo $this->query;
        return $this->connection->query($this->query);
    }
    //Delete a class via its ID
    public function deleteClass($id) {
        //Update all the students so that they are no longer in a class by setting their class ID to null
        $this->query = "SELECT * FROM `student` WHERE `ClassID` = ".$id;
        $students = $this->connection->query($this->query);
        foreach($students as $student) {
            $this->query = "UPDATE `student`
            SET `ClassID` = null
            WHERE `StudentID` = ".$student['StudentID'];
            $this->connection->query($this->query);
        }

        //Delete the class from record
        $this->query = "DELETE FROM `class` WHERE `ClassID` = ".$id;
        return $this->connection->query($this->query);
    }

    //#### Topic Related Queries ####
    //
    //
    //Fetch all topics
    public function getTopics() {
        $this->query = "SELECT * FROM `topic`";
        return $this->connection->query($this->query);
    }
    //Fetches a specific topic via its ID
    public function getTopicByID(int $id) {
        $this->query = "SELECT * FROM `topic` WHERE `TopicID` =".$id;
        return $this->connection->query($this->query);
    }
    //Fetch the recently completed topics for a student
    public function getRecentTopics($id) {
        $this->query = "SELECT * FROM result INNER JOIN topic ON result.TopicID = topic.TopicID WHERE StudentID = ".$id." ORDER BY DateCompleted DESC LIMIT 10";
        return $this->connection->query($this->query);
    }

    //#### Question Related Queries ####
    //
    //
    //Fetch the details of a question via its ID
    public function getQuestionByID($id) {
        $this->query = "SELECT * FROM `question` WHERE `QuestionID` = ".$id;
        return $this->connection->query($this->query);
    }
    //Fetches a set of questions for a topic via its ID
    public function getQuestionsByID(int $topicID) {
        $this->query = "SELECT * FROM `question` WHERE `TopicID` =".$topicID;
        return $this->connection->query($this->query);
    }

    //#### Assignment Related Queries ####
    //
    //   
    //Fetches a specific assignment via its ID
    public function getAssignmentByID(int $id) {
        $this->query = "SELECT * FROM `assignment` WHERE `AssignmentID` =".$id;
        return $this->connection->query($this->query);
    }
    //Fetch the assignments for a class that are not past the due date via a class ID
    public function getAssignmentsByClassID($id) {
        $this->query = "SELECT `AssignmentID`, `Date`, topic.TopicName FROM `assignment` INNER JOIN `topic` on assignment.TopicID = topic.TopicID WHERE `ClassID` = ".$id." AND `Date` > CURRENT_DATE()";
        $this->connection->query($this->query);
        echo $this->connection->error;
        return $this->connection->query($this->query);
    }
    //Create an assignment for a specific class with a topic and due date
    public function createAssignment($class, $topic, $dueDate) {
        $this->query = "INSERT INTO `assignment`(`ClassID`, `TopicID`, `Date`) VALUES(".$class.", ".$topic.", '".$dueDate."')";
        return $this->connection->query($this->query);
    } 
    //Fetch the past assignments for a class
    public function getPastAssignmentsByID($id) {
        $this->query = "SELECT AssignmentID, Date, topic.TopicName FROM assignment INNER JOIN topic on assignment.TopicID = topic.TopicID WHERE ClassID = ".$id." AND Date < CURRENT_DATE()";
        return $this->connection->query($this->query);
    }

    //#### Result Related Queries ####
    //
    //
    //Fetch the result for a student via their ID
    public function getResultsbyID($id) {
        $this->query = "SELECT * FROM result WHERE StudentID = ".$id;
        return $this->connection->query($this->query);
    }
    //Fetch the results for an assignment for a particular student
    public function getAssignmentResult($assignmentID, $studentID) {
        $this->query = "SELECT * FROM `result` WHERE `AssignmentID` = ".$assignmentID." AND `StudentID` = ".$studentID;
        return $this->connection->query($this->query);
    }  
    //Add the result for a particular topic with question history
    public function addTopicResult($topicID, $studentID, $questionsAnswered, $questionsCorrect, $questionOne, $questionOneResult, $questionTwo, $questionTwoResult, $questionThree, $questionThreeResult, $questionFour, $questionFourResult, $questionFive, $questionFiveResult) {
        $this->query = "INSERT INTO result(TopicID, StudentID, QuestionsAnswered, QuestionsCorrect, QuestionOneID, QuestionOneAnswer, QuestionTwoID, QuestionTwoAnswer,  QuestionThreeID, QuestionThreeAnswer, QuestionFourID, QuestionFourAnswer, QuestionFiveID, QuestionFiveAnswer) 
        VALUES (".$topicID.", ".$studentID.", ".$questionsAnswered.", ".$questionsCorrect.", ".$questionOne.", '".$questionOneResult."', ".$questionTwo.", '".$questionTwoResult."', ".$questionThree.", '".$questionThreeResult."', ".$questionFour.", '".$questionFourResult."', ".$questionFive.", '".$questionFiveResult."')";
        return $this->connection->query($this->query);
    }
    //Add the result for an assignment with question history that teachers can update
    public function addAssignmentResult($assignmentID, $topicID, $studentID, $questionsAnswered, $questionsCorrect, $questionOne, $questionOneResult, $questionTwo, $questionTwoResult, $questionThree, $questionThreeResult, $questionFour, $questionFourResult, $questionFive, $questionFiveResult) {
        $this->query = "INSERT INTO result(AssignmentID, TopicID, StudentID, QuestionsAnswered, QuestionsCorrect, QuestionOneID, QuestionOneAnswer, QuestionTwoID, QuestionTwoAnswer,  QuestionThreeID, QuestionThreeAnswer, QuestionFourID, QuestionFourAnswer, QuestionFiveID, QuestionFiveAnswer) 
        VALUES (".$assignmentID.", ".$topicID.", ".$studentID.", ".$questionsAnswered.", ".$questionsCorrect.", ".$questionOne.", '".$questionOneResult."', ".$questionTwo.", '".$questionTwoResult."', ".$questionThree.", '".$questionThreeResult."', ".$questionFour.", '".$questionFourResult."', ".$questionFive.", '".$questionFiveResult."')";
        $this->connection->query($this->query);
        return;
    }
}
?>