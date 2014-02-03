# Carbontally
---
Carbontally is used to capture data about cars, one by one, as they enter a car park such as a festival car park. It captures the following details about a car:

1. The car park - which is set once and will remain set until it is changed.
2. The fuel type (Petrol, Diesel, Hybrid).
3. The size of the car (S, M, L, Van).
4. The number of people in the vehicle (1-5 and 5+).
5. Whether or not the car is full.
6. Notes about the car.

It is a simple, mobile friendly, web application written in JQuery and PHP so it can be used on most modern smart phones and tablets. Carbontally also uses the appcache available on most modern browsers so it can be used without an internet connection (not always readily available in the field). Carbontally uses an upload feature to send the data to a server for permanent storage.

## Installing Carbontally
Carbontally is written in PHP and JQuery so there's no code to compile. To install the application simply deploy it to a web server cabable of running PHP 5.2 or 5.3.

## Database Configuration
Edit the dbconfig.php file and add your database server name, username, password and database name.
```php
$servername = 'localhost';
$username = '';
$password = '';
$database = '';
```
## Uploading Data
The security requirements for Carbontally weren't very strict so it uses a simple array of strings for passwords to allow users to upload data. If you want to offer Carbontally as a self service application then you will have to create a user security layer.

To add passwords for carbontally just add them to the `$userpwd` array in utility.php.
```php
$userpwd = array('your','passwords','here');
```
## ToDo
Carbontally would benefit from having the following features:
* A robust user security layer.
* Data export capabilities.
* Reporting.

There is the beginnings of reporting funtionality here in the code but the functionality is unfinished.

*Thanks for your interest, I hope this code is beneficial to someone. If not, I'm glad it was able to be used for the greater good once or twice.*
