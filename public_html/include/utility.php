<?php
$userpwd = array('1304','2181','7753','8079','8520','3318','7648','7203','8478','6878');
/*
 * JStoPHPBool
 * Converts a string 'true' or 'false' with any case into a php boolean TRUE or FALSE.
 * Throws an exception if the string does not represent a boolean.
 */
function JStoPHPbool($bool)
{
    switch(strtolower($bool))
    {
        case "true":
            return TRUE;
            break;
        case "false":
            return FALSE;
            break;
        default:
            throw new Exception("boolJStoPHP requires either 'TRUE' or 'FALSE'.");    
    }
}
?>
