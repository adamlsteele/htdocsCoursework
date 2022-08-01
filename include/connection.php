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
        $query = "SELECT * FROM ".$accountType." WHERE Email = '".$email."'";
        return $this->connection->query($query);
    }

    //Get account details from id
    public function getUserByID(int $id, $accountType) {
        switch($accountType) {
            case("student"):
                $this->query = "SELECT * FROM ".$accountType." WHERE StudentID = ?";
            case("teacher"):
                $this->query = "SELECT * FROM ".$accountType." WHERE TeacherID = ?";
        }

        $preparedStatement = $this->connection->prepare($this->query);
        $preparedStatement->bind_param("s", $email);

        $preparedStatement->execute();
        return $preparedStatement;
    }

    //Create an account of certain type for a user and then return the account details
    public function createAccount($email, $username, $password, $accountType) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO ".$accountType."(Email, Username, Password) VALUES('".$email."', '".$email."', '".$hashedPassword."')";

        $this->connection->query($query);

        //Return the created account details via a GET statement
        return $this->getUserByEmail($email, $accountType);
    }
}

?>