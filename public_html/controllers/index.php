<?php

require_once("../models/car.php");
require_once("../models/carDB.php");
require_once("../include/utility.php");
require_once("../include/errorhandler.php");

ini_set('max_execution_time', 900); //15 minute script timeout.

$carDB = new CarDB();

//Select the function based on the action string.
switch($_REQUEST['action']) {
    case 'getCars':
        getCarCount();
        break;
    case 'uploadCars':
        uploadCars();
        break;
}

/*
 * Gets the count of all the cars on the server.
 */
function getCarCount()
{
    global $carDB;

    $cars = new Cars($carDB);
    try{
    $carcount = $cars->getCarCount();
    }
    catch(Exception $e){
        error_log("Could not get car count " . $e->getMessage());
        throw $e;
    }

    echo json_encode($carcount);
}

/*
 * Gets an array of cars for the database and sends it to the client.
 */
function getCars()
{
    global $carDB;

    $cars = new Cars($carDB);

    $carlist = $cars->getCars();

    echo json_encode($carlist);
}

function getCarsByPassword($password){
    global $carDB;

    $cars = new Cars($carDB);

    $carlist = $cars->getCarsByPassword($password);

    echo json_encode($carlist);
}

//take all cars from local storage and save them to the database, ignoring cars that already exist.
function uploadCars()
{
    global $carDB;
    global $userpwd;
    
    //loop through all the cars from the client and save them
    //Strip extra quotes if magic quotes are turned on.
    if (get_magic_quotes_gpc()) {
        $carArray = json_decode(stripslashes($_POST["cars"]));
        $password = json_decode(stripslashes($_POST["pwd"]));
    }
    else{
        //get the variables from the client side car.
        $carArray = json_decode($_POST["cars"]);
        $password = json_decode($_POST["pwd"]);
    }
    
    if(in_array($password, $userpwd)){
        //just try to save them all because they are checked for existentce first and we only upload local cars.
        if($carArray){
            $carDB->openConnection();

            foreach($carArray as $car){
                try{
                    $currentCar = new Car($car->id, $car->carpark, $car->fuel, $car->size, JStoPHPbool($car->full), $car->num_of_passengers, $car->notes, $car->entry_date, $carDB);
                }catch(Exception $e){
                    throw new Exception("couldn't create new car: " . $e->getMessage());
                }

                try{
                    $currentCar->save($password);
                }catch(Exception $e){
                    throw new Exception("Couldn't save car: " . $e->getMessage());
                }
            }

            $carDB->closeConnection();
        }
        getCarsByPassword($password);
    }
    else{
        echo json_encode(0);    
    }
}
?>

