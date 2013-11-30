<?php
require("carDB.php");


/**
 * A car is a single automobile. Stores information about a car such as number of passengers and fuel type.
 *
 * @package carbontally
 * @since 0.3
 * @author schweeneh
 */
class Car {
    /*
     * The constructor for car.
     * @access public
     * $carDB - Injecting database object to make testing easier.
     */
    function __construct($id, $carpark, $fueltype, $carsize, $full, $number_of_passengers, $notes, $entry_date, CarDB $carDB)
    {
        try{
            $this->setCarID($id);
            $this->setCarPark($carpark);
            $this->setFuelType($fueltype);
            $this->setCarSize($carsize);
            $this->setFull($full);    
            $this->setNumberOfPassengers($number_of_passengers);    
            $this->setNote($notes);
            $this->setEntryDate($entry_date);
            $this->setCarDB($carDB);    
        }catch(Exception $e){
            throw new Exception("Could not create car: " . $e);
        }
    }

    /**
     *Save
     * Save a car to the database. 
     * Returns true on save and false if the car already exists.
     */
    public function save($password){
        $returnCode = false;
        
        $db = $this->getCarDB();
        
        try{
            $returnCode = $db->saveToDB($this->getCarID(),$this->getCarPark(),$this->getFuelType(), $this->getCarSize(), $this->getFull(), $this->getNumberOfPassengers(),$this->getNote(), $this->getEntryDate(),$password);    
        }catch(Exception $e){
            throw $e;
        }
        
        return $returnCode;
    }
    
    /*
     * Delete a car from the database.
     */
    public function delete(){
        $db = $this->getCarDB();
        try{
            $db->deleteFromDB($this->getCarID());
        }catch(Exception $e){
            throw $e;
        }
    }
        
    /*
     * carDB
     * 
     * The database object used to save car records to the database.
     */
    protected $carDB;
    
    public function getCarDB(){
        return $this->carDB;
    }
    
    /*
     * Sets the carDB object reference for database manipulation.
     */
    public function setCarDB($carDB){
        if(get_class($carDB) != "CarDB")
        {
            throw new Exception("carDB is not of type carDB.");
        }
        else{
            $this->carDB = $carDB;
        }
    }
    
    /**
     *carID 
     * 
     * Integer: Uniquely identifies the car.
     * @since 0.3
     * @access protected
     */
    protected $carID;
    
    public function getCarID() {
        return $this->carID;
    }
    
    public function setCarID($carID) {
        if(!is_numeric($carID)){
            throw new Exception("ID must be a number. ID is (" . $carID . ")");
        }
        else{
            error_log("Setting car ID to " . $carID);
            $this->carID = $carID;    
        }
    }
    
    /**
     *carID 
     * 
     * String: the name of the carpark that the car was in.
     * @since 0.3
     * @access protected
     */
    protected $carPark;
    
    public function getCarPark() {
        return $this->carPark;
    }
    
    public function setCarPark($carPark) {
        //error_log("Setting car ID to " . $carID);
        $this->carPark = $carPark;
    }
    
     /**
     * fueltype.
     * 
     * Enum: The fuel type of the car. Petrol, Diesel, Hybrid.
     * 
     * @since 0.3
     * @access protected
     */
    protected $fueltype;
    
    public function getFuelType() {
        return $this->fueltype;
    }

    public function setFuelType($fueltype) {
        if(!isset($fueltype) || !is_numeric($fueltype)){
            throw new Exception("Fuel type must be a number.");
        }
        else{
            $this->fueltype = $fueltype;    
        }
    }
    
     /**
     * carsize
     * 
     * Enum: The size of the car. Small, Medium, Large, Van
     * 
     * @since 0.3
     * @access protected
     */
    protected $carsize;
    
    public function getCarSize() {
        return $this->carsize;
    }

    public function setCarSize($carsize) {
        if(!is_numeric($carsize)){
            throw new Exception("Car size must be a number.");
        }
        else{
            $this->carsize = $carsize;
        }
    }
    
     /**
     * full
     * 
     * Boolean: Whether or not the car is full.
     * 
     * @since 0.3
     * @access protected
     */
    protected $full;
    
    public function getFull() {
        return $this->full;
    }

    public function setFull($full) {
        if(!is_bool($full)){
            throw new Exception("Full must be a boolean");
        }
        else{
            $this->full = $full;   
        }
    }
    
     /**
     * NumberOfPassengers
     * 
     * Enum: The number of passengers in the car. 99=more than 5.
     * 
     * @since 0.3
     * @access protected
     */
    protected $number_of_passegers;
    
    public function getNumberOfPassengers() {
        return $this->number_of_passegers;
    }

    public function setNumberOfPassengers($number_of_passengers) {
        if(!is_numeric($number_of_passengers)){
            throw new Exception("Number of passengers must be a number.");
        }
        else{
            $this->number_of_passegers = $number_of_passengers;
        }
    }
    
     /**
     * Note
     * 
     * String: A note about the car.
     * 
     * @since 0.3
     * @access protected
     */
    protected $note;
    
    public function getNote() {
        return $this->note;
    }

    public function setNote($note) {
        $this->note = $note;
    }
    
     /**
     * EntryDate
     * 
     * Datetime: The date and time that the entry was created.
     * 
     * @since 0.3
     * @access protected
     */
    protected $entry_date;
    
    public function getEntryDate() {
        return $this->entry_date;
    }

    public function setEntryDate($entry_date) {
        if(!date_parse($entry_date)){
            throw new Exception("Entry date must be a date.");
        }
        else{
            $this->entry_date = $entry_date;    
        }
        
    }
    
}

/**
 * A collection class for car.
 *
 * @package carbontally
 * @since 0.3
 * @author schweeneh
 */
class Cars{
    /*
     * The Cars constructor.
     */
    function __construct(CarDB $carDB)
    {
        try{
            $this->setCarDB($carDB);    
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /*
     * Gets the count of cars stored in the database.
     */
    function getCarCount(){
        $carDB = $this->getCarDB();
        
        $cars = $carDB->getCarCountAll();
        
        return $cars;
    }
    
    /*
     * Gets the array of rows from the db class and converts it into an array of cars.
     */
    function getCars()
    {
        $carDB = $this->getCarDB();
        
        $cars = $carDB->getCars();
        
        return $cars;
    }
    
    function getCarsByPassword($password){
        $carDB = $this->getCarDB();
        
        $cars = $carDB->getCarsByPassword($password);
        
        return $cars;
    }
    
    /*
     * carDB
     * 
     * The database object used to select cars from the database.
     */
    protected $carDB;
    
    public function getCarDB(){
        return $this->carDB;
    }
    
    public function setCarDB($carDB){
        if(get_class($carDB) != "CarDB")
        {
            throw new Exception("carDB is not of type carDB.");
        }
        else{
            $this->carDB = $carDB;
        }
    }
}
?>
