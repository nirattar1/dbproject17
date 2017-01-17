<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");
require_once("addValuesToTables.php");

// input
$inputDir = 'input/';
//$citiesInputFile = $inputDir."citiesInput.txt";





// getting ready... params:
$cityName = str_replace(',','',$cityName); //making sure there are no commas (the rest illegal chars is handled in the html page
$splitNum = 5; // = 25-30 requests for venues
// TODO: should I get it?
// $categotyId = ??
$loadToDB = 1;
$requestData = 1;

// foursquare
$foursquare = createNewFoursqaure('5');
// connet to DB
$conn = createConnection();

// search_venues
addNewCity($foursquare,$cityName,//$cityId,
		$jsonsDir,$venuesDir,$splitNum,$categotyId,$loadToDB,$requestData);

// new the new venue jsons are in jsons/venues/city_name
// load venues to DB


// search_menus_hours
searchHoursAndMenusPerCity($conn,$cityName)


// close connection to DB
closeConnection($conn);

?>
