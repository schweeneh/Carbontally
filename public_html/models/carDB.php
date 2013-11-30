<?php

require("../include/dbconfig.php");

class CarDB {
    /*
     * the mysqli connection.
     */
    protected $mysqli;   

    /*
     * open the carDB connection for use.
     */
    public function openConnection(){
        global $servername;
        global $username;
        global $password;
        global $database;
        
        $this->mysqli = new mysqli($servername,$username,$password,$database);
    }
    
    //closes the mysqli connection.
    public function closeConnection(){
        if(is_object($this->getGetCarCountStmt())){
            $this->closeGetCarCountStmt();
        }
        if(is_object($this->getInsertCarStmt())){
            $this->closeInsertCarStmt();
        }
        if(is_object($this->mysqli)){
            $this->mysqli->close();    
        }
    }
    
    /*
     * Save
     * Saves a car to the database.
     * Return true on save and false on no save.
     */
    public function saveToDB($id, $carpark, $fueltype, $carsize, $full, $number_of_passengers, $notes, $entry_date, $password){
        $returnCode = false;

        if($this->mysqli->connect_error){
            throw new Exception("Could not connect to database: (" . $this->mysqli->errno .") " . $this->mysqli->connect_error);
        }
    
        if($this->getCarCount($id, $password) == 0){
            //if it doesn't already exist then insert it.
            $this->insertCar($id, $password, $carpark, $fueltype, $carsize, $full, $number_of_passengers, $notes, $entry_date);
            $returnCode = true;
        }
        else{
            //if it already exists then send a code back to calling code.
            $returnCode = false;
        }

        return $returnCode;
    }
    
    /*
     * Stored statement for the getCarCount query.
     */
    protected $getCarCountStmt;
    
    public function getGetCarCountStmt() {
        return $this->getCarCountStmt;
    }

    public function setGetCarCountStmt($stmt) {
        $this->getCarCountStmt = $stmt;    
    }
    
    //close the getcarcountstmt.
    public function closeGetCarCountStmt(){
        if($this->getCarCountStmt){
            $this->getCarCountStmt->close();
        }
    }
    
    //Gets the count of all cars in the database.
    public function getCarCountAll(){
        global $servername;
        global $username;
        global $password;
        global $database;
        $carcount = 0;

        //create a connection to MySQL.
        $mysqli = new mysqli($servername,$username,$password,$database);
        
        if($mysqli->connect_error){
            throw new Exception("Could not connect to database: (" . $mysqli->errno .") " . $mysqli->connect_error);
        }

        if(!($stmt = $mysqli->prepare("SELECT count(*) as count FROM cars"))){
            throw new Exception("getCarCountAll Prepare statement failed: (" . $mysqli->errno . ")" . $mysqli->error);
        }
                        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $mysqli->errno . ") " . $mysqli->error);
        }
        
         //have to use bind_result and fetch because mysqlnd isn't installed on 1and1.
        $stmt->bind_result($count);
        
        while ($stmt->fetch()){
            $carcount = $count;
        }

        $stmt->close();

        return $carcount;
    }
    
    //gets the count of cars with a specific ID.
    protected function getCarCount($id, $password)
    {
        $carcount = 0;
        
        //cache the $stmt if it doesn't exist yet so it can be reused.
        //Check to see if the car id already exists.
        if(!is_object($this->getGetCarCountStmt())){
            error_log("getCarCountStmt not set, setting now.");
            if(!($stmt = $this->mysqli->prepare("SELECT count(*) as count FROM cars WHERE id = ? AND password = ?"))){
                throw new Exception("getCarCount Prepare statement failed: (" . $this->mysqli->errno . ")" . $this->mysqli->error);
            }
            $this->setGetCarCountStmt($stmt);
        }
        else{
            $stmt = $this->getGetCarCountStmt();
        }
        
        if(!$stmt->bind_param("ss", $id, $password)) {
            throw new Exception("Binding parameters failed: " . $this->mysqli->errno . ")" . $this->mysqli->error);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
        }
        //have to use bind_result and fetch because mysqlnd isn't installed on 1and1.
        $stmt->bind_result($count);
        while ($stmt->fetch()){
            //error_log("Getting count: " . $count. " with id " . $id,0);
            $carcount = $count;
        }

        return $carcount;
    }
    
    /*
     * Stored statement for the insertcar query.
     */
    protected $insertCarStmt;
    
    public function getInsertCarStmt() {
        return $this->insertCarStmt;
    }

    public function setInsertCarStmt($stmt) {
        $this->insertCarStmt = $stmt;    
    }
    
    //close the insert car statement.
    public function closeInsertCarStmt(){
        if($this->getInsertCarStmt()){
            $this->insertCarStmt->close();
        }
    }
    
    protected function insertCar($id, $password, $carpark, $fueltype, $carsize, $full, $numofpassengers, $notes, $entrydate)
    {
        //cache the insert statement for later use.
        if(!is_object($this->getInsertCarStmt())){
            if(!($stmt = $this->mysqli->prepare("INSERT INTO cars(id, password, carpark, fueltype, carsize, full, numofpassengers, notes, entrydate) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)"))){
                throw new Exception("Prepare statement failed: (" . $this->mysqli->errno . ")" . $this->mysqli->error);
            }
            $this->setInsertCarStmt($stmt);
        }
        else{
            $stmt = $this->getInsertCarStmt();
        }
        
        if(!$stmt->bind_param("sssiiiiss", $id, $password, $carpark, $fueltype, $carsize, $full, $numofpassengers, $notes, $entrydate)) {
            throw new Exception("Binding parameters failed: " . $this->mysqli->errno . ")" . $this->mysqli->error);
        }

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error);
        }
    }
    
    /*
     * Gets all the cars from the database.
     */
    public function getCars()
    {
        global $servername;
        global $username;
        global $password;
        global $database;
        $carRows = 0;
        
        //create a connection to MySQL.
        $mysqli = new mysqli($servername,$username,$password,$database);
        
        if($mysqli->connect_error){
            throw new Exception("Could not connect to database: (" . $mysqli->errno .") " . $mysqli->connect_error);
        }

        if(!($stmt = $mysqli->prepare("SELECT * FROM cars"))){
            throw new Exception("Prepare statement failed: (" . $mysqli->errno . ")" . $mysqli->error);
        }
                
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $mysqli->errno . ") " . $mysqli->error);
        }
        
         //have to use bind_result and fetch because mysqlnd isn't installed on 1and1.
        $stmt->bind_result($id, $pwd, $carpark, $fueltype, $carsize, $full, $numofpassengers, $notes, $entrydate, $lastupdate);
        
        while ($stmt->fetch()){
            $car = array("id"=>$id, "fuel"=>$fueltype, "size"=>$carsize, "full"=>$full, "num_of_passengers"=>$numofpassengers, "note"=>$notes, "entry_date"=>$entrydate, "local"=>FALSE);
            $carRows[] = $car;
        }
        
        $stmt->close();
        
        return $carRows;
    }
    
    /*
     * Gets all the cars from the database.
     */
    public function getCarsByPassword($pwd)
    {
        global $servername;
        global $username;
        global $password;
        global $database;
        
        $carRows = 0;
        
        //create a connection to MySQL.
        $mysqli = new mysqli($servername,$username,$password,$database);
        
        if($mysqli->connect_error){
            throw new Exception("Could not connect to database: (" . $mysqli->errno .") " . $mysqli->connect_error);
        }

        if(!($stmt = $mysqli->prepare("SELECT * FROM cars WHERE password = ?"))){
            throw new Exception("Prepare statement failed: (" . $mysqli->errno . ")" . $mysqli->error);
        }
        
        if(!$stmt->bind_param("s", $pwd)) {
            throw new Exception("Binding parameters failed: " . $this->mysqli->errno . ")" . $this->mysqli->error);
        }
                
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $mysqli->errno . ") " . $mysqli->error);
        }
        
         //have to use bind_result and fetch because mysqlnd isn't installed on 1and1.
        $stmt->bind_result($id, $pwd, $carpark, $fueltype, $carsize, $full, $numofpassengers, $notes, $entrydate, $lastupdate);
        
        while ($stmt->fetch()){
            $car = array("id"=>$id, "fuel"=>$fueltype, "size"=>$carsize, "full"=>$full, "num_of_passengers"=>$numofpassengers, "note"=>$notes, "entry_date"=>$entrydate, "local"=>FALSE);
            $carRows[] = $car;
        }

        $stmt->close();

        return $carRows;
    }
}
?>