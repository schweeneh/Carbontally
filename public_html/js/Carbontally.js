//***************Carbontally************************//

//
//Carbontally global variable.
//
var CT = {};

//
//Test for local storage.
//
CT.supports_html5_storage = function(){
    if(Modernizr.localstorage)
    {
        return "Congratulations, your browser supports local storage!";
    }
    else
    {
        return "Your browser does not support local storage.";
    }
};

//saves the carpark object to local storage.
CT.setCarPark = function(cName){
    var carPark = {carParkName: cName};
    
    localStorage.setObject("carpark", carPark);
}

//returns the ID that the carpark is set to.
/*CT.getCarPark = function(){
    var carPark = localStorage.getObject("carpark");
    if(carPark){
        return carPark['carParkID'];    
    }
}*/

//returns the name of the carpark if it is set, otherwise returns undefined.
CT.getCarParkName = function(){
    var carPark = localStorage.getObject("carpark");
    if(carPark){
        return carPark['carParkName'];
    }else{
        return undefined;
    }
}

//Save the password to local storage.
CT.setPassword = function(password){
    localStorage.setItem("password", password);
}

//Get the password from local storage.
CT.getPassword = function(){
    return localStorage.getItem("password");
}

//return the name of the carpark based on the id.
/*CT.carPark = function(carPark){
    switch(parseInt(carPark)){
        case 1:
            return "Red";
            break;
        case 2:
            return "Yellow";
            break;
        case 3:
            return "Green";
            break;
    }
}*/

//return the text for fueltype based on the id.
CT.fuelType = function(fuelType){
    switch(parseInt(fuelType)){
        case 1:
            return "Petrol";
            break;
        case 2:
            return "Diesel";
            break;
        case 3:
            return "Hybrid";
            break;
    }
}

//return the text for size based on the id.
CT.carSize = function(carSize){
    switch(parseInt(carSize)){
        case 1:
            return "Small";
            break;
        case 2:
            return "Medium";
            break;
        case 3:
            return "Large";
            break;
        case 4:
            return "Van";
            break;
    }
}

//return the number of passengers.
CT.numberOfPassengers = function(numberOfPassengers){
    switch(parseInt(numberOfPassengers)){
        case 99:
            return "more than 5";
            break;
        default:
            return numberOfPassengers;
    }
}

CT.formatDate = function(date1) {
  return date1.getFullYear() + '-' +
    (date1.getMonth() < 9 ? '0' : '') + (date1.getMonth()+1) + '-' +
    (date1.getDate() < 10 ? '0' : '') + date1.getDate() + " " + 
    (date1.getHours() < 10 ? '0' : '') + date1.getHours() + ":" + 
    (date1.getMinutes() < 10 ? '0' : '') + date1.getMinutes() + ":" + 
    (date1.getSeconds() < 10 ? '0' : '') + date1.getSeconds();
}

//Sort function to sort dates in descending order.
CT.sortCarsByDateDesc = function(car1, car2){
    return Date.parse(car2.entry_date)-Date.parse(car1.entry_date);
}

//
//Stringify an object and add it to local storage.
//
Storage.prototype.setObject = function(key, value){
    this.setItem(key, JSON.stringify(value));
}

//
//Retrieve an object from local storage and parse it.
//
Storage.prototype.getObject = function(key) {
    var value = this.getItem(key);
    //This is done so that JSON.parse won't throw an error with a null return.
    return value && JSON.parse(value);
}    

//
//Car class.
//
CT.Car = function(carpark, fuel, size, full, num_of_passengers, notes, entry_date){
    this.carpark = carpark;
    this.fuel = fuel;
    this.size = size;
    this.full = full;
    this.num_of_passengers = num_of_passengers;
    this.notes = notes;
    this.entry_date = entry_date;
    //The new date is the time since epoch in milliseconds. 
    //We're going to use a unique userID to make sure each entry is unique.
    this.id = +new Date() + '';
    this.local = false;
    this.deleted = false;
}

//
//Save car locally method.
//Description: Saves the car locally using HTML5 localStorage.
//Uses the car's id as the localStorage id.
//savedRemotely - 'true' if the car was also saved remotely, otherwise 'false';
//
CT.Car.prototype.saveLocally = function(){
    try{
        localStorage.setObject(this.id, this)
    }
    catch(e){
        throw{
            name: 'Error',
            message: "You may have run out of storage space, upload cars to continue. " + e.message
        }
    }
}

//
//Deletes a car from localStorage based on the cars id
//which is the localStorage key.
//
CT.Car.prototype.deleteFromLocal = function (){
    //try to delete from localStorage based on Car ID.
    try{
        localStorage.removeItem(this.id);
    }
    catch(e){
       throw{
            name: 'Error',
            message: "Could not delete car. " + e.message
       }
    }
}

//
//Try to save the car to the database. If that fails because there is no connectivity (or for any other reason really) then save it locally.
//
CT.Car.prototype.save = function(options){
    //try to save
    var c = this;
    c.local = true;
    try{
        c.saveLocally();
        options['savedLocally']();
    }
    catch(e){
        alert("Could not save car locally. " + e.message);
    }
    options['done']();
//    $.ajax({
//        url: "/carbontally/public_html/controllers/newentry.php",
//        //url: "/controllers/newentry.php",
//        type: "POST",
//        dataType: "json",
//        data: {action: "save", car : JSON.stringify(c)},
//        success: function(data, textStatus, jqXHR){
//          //the car has been saved to the database.
//          //if it exists locally then delete it.
//          //data is a car object.
//          //c.deleteFromLocal();
//          c.local = false;
//          options['savedRemotely']();
//          //alert(data + ' ' + textStatus);
//        },
//        error: function(jqXHR, textStatus, errorThrown){
//          //if fail due to network failure save locally.
//          try{
//            c.local = true;
//            options['savedLocally']();
//          }
//          catch(e){
//              alert("Could not save car locally. " + e.message);
//          }
//          //alert(textStatus + ": " + errorThrown + ' ' + jqXHR.responseText)
//        },
//        complete: function(jqXHR, textStatus){
//            c.saveLocally();
//            options['done']();
//        }
//    });
}

//Makes an ajax call to get the list of cars from the database and the adds a listitem for each car.
CT.getCars = function(displayCars){
    $.mobile.showPageLoadingMsg("b","Loading...",true);
    var cars;
    
     $.ajax({
        url: "/carbontally/public_html/controllers/index.php",
        //url: "/controllers/index.php",
        type: "POST",
        dataType: "json",
        data: {action: "getCars"},
        success: function(data, textStatus, jqXHR){
            //return the car list.
            var cars = CT.getLocalCars();
            displayCars(cars, data);
        },
        error: function(jqXHR, textStatus, errorThrown){
          //if getting cars from the database fails, get local cars instead.
          //alert(textStatus + ": " + errorThrown + ' ' + jqXHR.responseText)
          try{
            console.log(textStatus + ' ' + errorThrown);
            var cars = CT.getLocalCars();
            displayCars(cars);            
          }
          catch(e){
              alert("Couldn't get cars from local storage. " + e.message);
          }
        }
    });
}

//updoads all local cars to the database.
CT.uploadCars = function(callback, password){
    $.mobile.showPageLoadingMsg("b","Uploading, please wait...",true);
     //get the cars from local storage.
     var carArray = CT.getLocalCars();

     $.ajax({
        url: "/carbontally/public_html/controllers/index.php",
        //url: "/controllers/index.php",
        type: "POST",
        dataType: "json",
        data: {action: "uploadCars", cars: JSON.stringify(carArray), pwd: password},
        success: function(data, textStatus, jqXHR){
            //delete all but most recent 50 cars.
            if(data == 0){
                alert("Incorrect Password");
            }
            else{
                CT.deleteAllLocalCars(data);
                callback();    
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            //cars were not uploaded please try again later.
            alert("Cars were not uploaded successfully, please try again later. " + textStatus + ": " + errorThrown);
        }
    });
}

CT.testLocalStorageSize = function(){
    //test inserting 10000 cars into local storage.
     for(var i=0;i<10000;i++){
        var car = new CT.Car("1","1","3","TRUE","99","asdf akdsjf asdfkj", CT.formatDate(new Date()));
        car.saveLocally();
    }
}
//deletes all but the most recent 50 cars. Sets the most recent 50 to remote status.
CT.deleteAllButMostRecent50Cars = function(){
    //get the list of cars from local.
    var cars = CT.getLocalCars();
    //order them by date.
    cars.sort(CT.sortCarsByDateDesc);
    //loop through the most recent 50 setting their status to remote.
    for(var i=0;i<cars.length;i++){
        if(i<50){
            cars[i].local=false;
            cars[i].saveLocally();
        }
        else{
            //delete all but the most recent 50.
            cars[i].deleteFromLocal();
        }
    }
}

//deletes the cars only if they were successfully stored and then retrieved from the server.
CT.deleteAllLocalCars = function(cars){
    if(cars){
        for(var i=0;i<cars.length;i++){
            //delete localStorage.getObject(cars[i].id+0);
            localStorage.removeItem(cars[i].id);
        }
    }
}
//Makes an ajax call to get the list of cars from the database and the adds a listitem for each car.
//CT.deleteCar = function(options){
//     $.ajax({
//        //url: "/carbontally/public_html/controllers/index.php",
//        url: "/controllers/index.php",
//        type: "POST",
//        dataType: "json",
//        data: {action: "deleteCar", id: ""},
//        success: function(data, textStatus, jqXHR){
//            //refresh the listview.
//            
//        },
//        error: function(jqXHR, textStatus, errorThrown){
//          //Could not delete car. Flag the local car for deletion on sync.
//          try{
//            var cars = CT.getLocalCars();
//            displayCars(cars);
//          }
//          catch(e){
//              alert("Couldn't get cars from local storage. " + e.message);
//          }
//        }
//    });
//}

//Get the cars from local storage.
//returns an array of cars.
CT.getLocalCars = function()
{
    var cars = new Array();
    
    for(var i=0; i<localStorage.length;i++){
        var car = localStorage.getObject(localStorage.key(i));
        //lets make sure it's a car before adding it to the array.
        if(car.hasOwnProperty('fuel')){
            cars[cars.length] = car;
        }
    }
    
    return cars;
}

//gets a single car by it's ID.
CT.getCarByID = function(carID){
    return localStorage.getObject(carID);
}

//delete a car by ID.
CT.deleteCarByID = function(carID){
    localStorage.removeItem(carID);
}

//****************Layout************************************************/
//Register event for hiding the ajax loading image.
$(document).ajaxComplete(function(){
    $.mobile.hidePageLoadingMsg();
});

//this section deals with the layout in protrait and landscape layouts.
$(document).bind("pageinit", function(e) {
    CT.changeLayout(e.orientation);
    $(window).bind('orientationchange',function(e) {
        CT.changeLayout(e.orientation);
    });
});

//Changes the control layout based on the current orientation.
CT.changeLayout = function(orientation){
    if(orientation == 'landscape'){
        //In portrait mode, all radio buttons should be stacked next to each other and be vertical.
        $('#grid-a').addClass('ui-grid-a');
        $('#grid-cell-a').addClass('ui-block-a');
        $('#grid-cell-b').addClass('ui-block-b');
        $('#grid-cell-c').addClass('ui-block-a');
        $('#grid-cell-d').addClass('ui-block-b');
    }
    else{
        //In landscape mode, all radio buttons should be underneath each other and be horizontal.
        $('#grid-a').removeClass('ui-grid-a');
        $('#grid-cell-a').removeClass('ui-block-a');
        $('#grid-cell-b').removeClass('ui-block-b');
        $('#grid-cell-c').removeClass('ui-block-a');
        $('#grid-cell-d').removeClass('ui-block-b');
    }
}