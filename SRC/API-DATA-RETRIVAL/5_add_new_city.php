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
	$cityName = ucwords(strtolower(str_replace(',','',$cityName))); //making sure there are no commas (the rest illegal chars is handled in the html page + first letter capital and the rest is lower case
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
	userFeedback('This will take a minute...');
	
	$cityId = getCityIdByName($conn,$cityName);
	fwrite($writeLogs,date("H:i:s")." - cityId=$cityId\r\n");
	
	// now the new venue jsons are in jsons/venues/city_name
	// load venues to DB (11_parse_venues.php)
	userFeedback('Getting retaurants');
	loadVenuesPerCity($jsonsDir,$venuesDir,$cityNameDir,$cityId,$loadToDB,$conn);
	fwrite($writeLogs,date("H:i:s")." - after loadVenuesPerCity\r\n");

	// search_menus_hours (2_search_menus_hours.php)
	searchHoursAndMenusPerCity($conn,$foursquare,$jsonsDir,$cityNameDir,true);
	fwrite($writeLogs,date("H:i:s")." - after searchHoursAndMenusPerCity\r\n");

	// load menus to DB (21_parse_menus.php)
	loadMenusPerCity($jsonsDir,$menusDir,$cityNameDir,$loadToDB,$conn);
	fwrite($writeLogs,date("H:i:s")." - after loadMenusPerCity\r\n");
	
	//optimize the dish table (just in case many data was inserted and index should be reuilt)
	optimizeDishTable($conn);
	
	// load hours to DB (31_parse_hours.php)
	loadHoursPerCity($jsonsDir,$hoursDir,$cityNameDir,$loadToDB,$conn);
	fwrite($writeLogs,date("H:i:s")." - after loadHoursPerCity\r\n");

	
	// close connection to DB
	closeConnection($conn);

	userFeedback('Done!');	
	return true;
}


function deleteCityData($conn,$cityId){
	// delete entries:
	$deleteDishes = "delete from Dish
					where Dish.restaurant_id in
					(select Restaurant.id 
					from Restaurant
					where Restaurant.city_id=$cityId)";
					
	$deleteHours = "delete from OpenHours
					where OpenHours.restaurant_id in
					(select Restaurant.id 
					from Restaurant
					where Restaurant.city_id=$cityId)";
					
	$deleteVenues = "delete from Restaurant where city_id=$cityId";

	$deleteCity = "delete from City where id=$cityId";
	
	if( ! $conn->query($deleteDishes)){
		return false;
	}
	if( ! $conn->query($deleteHours)){
		return false;
	}
	if( ! $conn->query($deleteVenues)){
		return false;
	}
	if( ! $conn->query($deleteCity)){
		return false;
	}
	
	return true;
}



?>
