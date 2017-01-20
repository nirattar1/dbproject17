<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");
require_once("addValuesToTables.php");

// input
$inputDir = 'input/';


addNewCity('Miami',$jsonsDir,$venuesDir);

function addNewCity($cityName,$jsonsDir,$venuesDir){
	$writeLogs = fopen('5_logs.txt','w');
	
	// params:
	$cityName = str_replace(',','',$cityName); //making sure there are no commas (the rest illegal chars is handled in the html page
	$splitNum = 5; // = 25-30 requests for venues
	$categoryId = "4d4b7105d754a06374d81259"; // main food categoryId
	$loadToDB = 1;
	$requestData = 1;

	// foursquare
	$foursquare = createNewFoursqaure('5');
	// connet to DB
	$conn = createConnection();
	$cityNameDir = str_replace('_',' ',$cityName);


	// -- start --
	fwrite($writeLogs,date("H:i:s")." - start\r\n");

	// search_venues
	$cityId = addNewCity($foursquare,$cityName, // cityName - no underscore
			$jsonsDir,$venuesDir,$splitNum,$categoryId,$loadToDB,$requestData);
	fwrite($writeLogs,date("H:i:s")." - after addNewCity\r\n");


	// now the new venue jsons are in jsons/venues/city_name
	// load venues to DB
	loadVenuesPerCity($jsonsDir,$venuesDir,$cityNameDir,$cityId,$loadToDB,$conn);
	fwrite($writeLogs,date("H:i:s")." - after loadVenuesPerCity\r\n");

	// search_menus_hours
	searchHoursAndMenusPerCity($conn,$cityNameDir);
	fwrite($writeLogs,date("H:i:s")." - after searchHoursAndMenusPerCity\r\n");

	// load menus to DB
	loadMenusPerCity($jsonsDir,$menusDir,$cityNameDir,$loadToDB,$conn);
	fwrite($writeLogs,date("H:i:s")." - after loadMenusPerCity\r\n");

	// load hours to DB
	loadHoursPerCity($jsonsDir,$hoursDir,$cityNameDir,$loadToDB,$conn)
	fwrite($writeLogs,date("H:i:s")." - after loadHoursPerCity\r\n");

	// close connection to DB
	closeConnection($conn);
}


?>
