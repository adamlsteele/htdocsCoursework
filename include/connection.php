<?php
class Connection {
    //First calls when an object is made based on the class
    //Sets up a connection using a connection string
    private $connection;

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

    //Peforms GET queries returning the statement result
    //Arguements include the statement query, prepared statement variable types and values
    public function executeQuery($query, $paramTypes, array $paramValues) {
        $preparedStatement = $this->connection->prepare($query);
        $preparedStatement->bind_param($paramTypes, $paramValues);
        $preparedStatement->execute();
        return $preparedStatement;
    }
}

?>