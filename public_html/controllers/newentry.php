<?php

require_once("../models/car.php");
require_once("../models/carDB.php");
require_once("../include/utility.php");
require_once("../include/errorhandler.php");

$carDB = new CarDB();

//Select the function based on the action string.
switch($_POST['action']) {
    case 'save': 
        save();
        break;
    case 'getCars':
        getCars($carDB);
        break;
}

//save the car to the database.
function save(){
    //full is a boolean and since it comes from JavaScript and is not converted by JSON it must be first converted to a PHP bool.
    $full = FALSE;
    
    try{
        //Strip extra quotes if magic quotes are turned on.
        if (get_magic_quotes_gpc()) {
            $car_vars = json_decode(stripslashes($_POST["car"]));
        }
        else{
            //get the variables from the client side car.
            $car_vars = json_decode($_POST["car"]);
        }

        //create an instance of the car database class.
        $carDB = new CarDB();
        
        $carDB->openConnection();
        
        //create a new car.
        $car = new Car($car_vars->id,$car_vars->fuel,$car_vars->size,JStoPHPBool($car_vars->full),$car_vars->num_of_passengers,$car_vars->notes,$car_vars->entry_date,$carDB);
        //save the car.
        $car->save();
        
        $carDB->closeConnection();
    }catch(Exception $e){
        throw $e;
    }
}

/*
 * Gets an array of cars for the database and sends it to the client.
 */
function getCars($carDB)
{
    $cars = $carDB->getCars();

    echo json_encode($cars);
}
?>
