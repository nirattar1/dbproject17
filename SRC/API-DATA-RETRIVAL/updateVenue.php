<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");
require_once("addValuesToTables.php");


// Set your client key and secret
$client_key = "PNQBKVKJSRGNN4NXJGMU2J2X1OXWLGM2W5ARZDYDAPUXEJWN";
$client_secret = "CXUSCYJ14XAMKCNYQFLQ2LB45HCAORYHQKDENQQTGGEGJMTB";
// Load the Foursquare API library

if($client_key=="" or $client_secret=="")
{
    echo 'Load client key and client secret from <a href="https://developer.foursquare.com/">foursquare</a>';
    exit;
}

$foursquare = new FoursquareApi($client_key,$client_secret);

$requestType = "venues/$id";
$params = array();
$nameParams = array($id."update");

getAndSaveJSON($foursquare,$requestType,$params,$nameParams,$jsonsDir.$updatesDir);

?>