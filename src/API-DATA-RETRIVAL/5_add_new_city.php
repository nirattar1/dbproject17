<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");
require_once("1_search_venues.php");
require_once("11_parse_venues.php");
require_once("2_search_menus_hours.php");
require_once("21_parse_menus.php");
require_once("31_parse_hours.php");
require_once("addValuesToTables.php");


function addNewCityStory($cityName,$jsonsDir='jsons/',$venuesDir='venues/',$menusDir='menus/',$hoursDir='hours/'){ // allowing default params
	$writeLogs = fopen('5_logs.txt','w');
	
	// params:
	$cityName = str_replace(',','',$cityName); //making sure there are no commas (the rest illegal chars is handled in the html page
	$splitNum = 2; // = 4-9 requests for venues
	$categoryId = "4d4b7105d754a06374d81259"; // main food categoryId
	$loadToDB = 1;
	$requestData = 1;

	// foursquare
	$foursquare = createNewFoursqaure('5');
	// connet to DB
	$conn = createConnection();
	$cityNameDir = str_replace(' ','_',$cityName);


	// -- start --
	fwrite($writeLogs,date("H:i:s")." - start\r\n");

	// search_venues (1_search_venues.php)
	list($isOk,$errorStr) = addNewCity($foursquare,$cityName, // cityName - no underscore
			$jsonsDir,$venuesDir,$splitNum,$categoryId,$loadToDB,$requestData,$conn);
	fwrite($writeLogs,date("H:i:s")." - after addNewCity\r\n");
	
	if($isOk === false){
		return $errorStr;
	}
	
	$cityId = getCityIdByName($conn,$cityName);
	fwrite($writeLogs,date("H:i:s")." - cityId=$cityId\r\n");
	
	// now the new venue jsons are in jsons/venues/city_name
	// load venues to DB (11_parse_venues.php)
	loadVenuesPerCity($jsonsDir,$venuesDir,$cityNameDir,$cityId,$loadToDB,$conn);
	fwrite($writeLogs,date("H:i:s")." - after loadVenuesPerCity\r\n");

	// search_menus_hours (2_search_menus_hours.php)
	searchHoursAndMenusPerCity($conn,$jsonsDir,$cityNameDir);
	fwrite($writeLogs,date("H:i:s")." - after searchHoursAndMenusPerCity\r\n");

	// load menus to DB (21_parse_menus.php)
	loadMenusPerCity($jsonsDir,$menusDir,$cityNameDir,$loadToDB,$conn);
	fwrite($writeLogs,date("H:i:s")." - after loadMenusPerCity\r\n");

	// load hours to DB (31_parse_hours.php)
	loadHoursPerCity($jsonsDir,$hoursDir,$cityNameDir,$loadToDB,$conn);
	fwrite($writeLogs,date("H:i:s")." - after loadHoursPerCity\r\n");

	// close connection to DB
	closeConnection($conn);
	
	return true;
}

?>
