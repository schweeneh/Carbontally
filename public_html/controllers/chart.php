<?php
require_once("../models/car.php");
require_once("../models/carDB.php");
require_once("../include/utility.php");
require_once("../include/errorhandler.php");

$carDB = new CarDB();

//Select the function based on the action string.
switch($_REQUEST['chart']) {
    case 'fuelType':
        getFuelTypeChart();
        break;
}

// Set the JSON header
header("Content-type: text/json");

/*
 * Retrieves the data for the fuel type chart.
 */
function getFuelTypeChart(){
    global $carDB;

    $cars = new Cars($carDB);

    $carlist = $cars->getCars();
    
    $DieselCount = 0;
    $HybridCount = 0;
    $PetrolCount = 0;
    
    foreach($carlist as $car){
        switch($car['fuel']){
            case 1:
                $PetrolCount++;
                break;
            case 2:
                $DieselCount++;
                break;
            case 3:
                $HybridCount++;
                break;
        }
    }
    
    //The x-axis is the fuel type.
    //$points = array(array("Petrol",$PetrolCount),array("Diesel",$DieselCount),array("Hybrid", $HybridCount));
    $points = array(array('Petrol',$PetrolCount),array('Diesel',$DieselCount),array('Hybrid', $HybridCount));
    // Create a PHP array and echo it as JSON
    echo json_encode($points);
}
?>
