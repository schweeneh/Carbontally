<?php
//set the exception handler.
function handleException( $e ) {
       header("HTTP/1.1 500 Internal Server Error");
       writeToErrorLog($e->getMessage());
       echo json_encode($e->getMessage());
 }
 
 //write to the error log.
 function writeToErrorLog($errorMessage){
     $date = new DateTime(date(), new DateTimeZone("Europe/London"));
     error_log($date->format(DATE_W3C) . " " . $errorMessage . "\n",3,"../logs/errors.log");
 }
 
set_exception_handler( 'handleException' );
?>
