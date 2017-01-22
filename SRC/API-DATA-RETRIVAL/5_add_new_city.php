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


function addNewCityStory($cityName){
	// connet to DB
	$conn = createConnection();
	if ($conn === false)
		return "Connection error";

	$res = addNewCityStoryBackend($conn,$cityName);
	// error handling
	if($res !== true){
		// in case of an error will need to delete all the data that was loaded until the error occured
		list($cityId,$errorStr) = $res;
		deleteCityData($conn,$cityId);
		
		// close connection to DB
		closeConnection($conn);
		
		return $errorStr;
	}else{
	
		// close connection to DB
		closeConnection($conn);
		
		return true; // good!
	}
}


function addNewCityStoryBackend($conn,$cityName,$jsonsDir='jsons/',$venuesDir='venues/',$menusDir='menus/',$hoursDir='hours/'){ // allowing default params
	$writeLogs = fopen('5_logs.txt','w');
	$cityId = null;
	
	// params:
	$cityName = ucwords(strtolower(str_replace(',','',$cityName))); //making sure there are no commas (the rest illegal chars is handled in the html page + first letter capital and the rest is lower case
	$splitNum = 2; // = 4-9 requests for venues
	$categoryId = "4d4b7105d754a06374d81259"; // main food categoryId
	$loadToDB = 1;
	$requestData = 1;

	// foursquare
	$foursquare = createNewFoursqaure('5');
	$cityNameDir = str_replace(' ','_',$cityName);


	// -- start --
	fwrite($writeLogs,date("H:i:s")." - start\r\n");

	// search_venues (1_search_venues.php)
	list($isOk,$errorStr) = addNewCity($foursquare,$cityName, // cityName - no underscore
			$jsonsDir,$venuesDir,$splitNum,$categoryId,$loadToDB,$requestData,$conn);
	fwrite($writeLogs,date("H:i:s")." - after addNewCity\r\n");
	
	
	if($isOk === false){
		return array($cityId,$errorStr); // return error
	}
	if(isLimitExceeded($foursquare)){
		// never should happen but we don't take risks. still, we'll do it just once, before the big amount of requsts. (if limit exceeded later it will be handled in other ways)
		return array($cityId,"Limit exceeded. Come Back in an hour."); 
	}
	
	$cityId = getCityIdByName($conn,$cityName);
	fwrite($writeLogs,date("H:i:s")." - cityId=$cityId\r\n");
	
	// now the new venue jsons are in jsons/venues/city_name
	// load venues to DB (11_parse_venues.php)
	$isOk = loadVenuesPerCity($jsonsDir,$venuesDir,$cityNameDir,$cityId,$loadToDB,$conn);
	fwrite($writeLogs,date("H:i:s")." - after loadVenuesPerCity\r\n");
	
	if($isOk === false){
		return array($cityId,"Failed to load restaurants"); // return error
	}

	// search_menus_hours (2_search_menus_hours.php)
	$isOk = searchHoursAndMenusPerCity($conn,$foursquare,$jsonsDir,$cityNameDir);
	fwrite($writeLogs,date("H:i:s")." - after searchHoursAndMenusPerCity\r\n");

	if($isOk !== true){
		return array($cityId,$isOk); // return error ($isOk is the error string in this case)
	}
	
	// load menus to DB (21_parse_menus.php)
	loadMenusPerCity($jsonsDir,$menusDir,$cityNameDir,$loadToDB,$conn);
	fwrite($writeLogs,date("H:i:s")." - after loadMenusPerCity\r\n");

	if($isOk === false){
		return array($cityId,"Failed to load dishes"); // return error
	}	

	
	// load hours to DB (31_parse_hours.php)
	loadHoursPerCity($jsonsDir,$hoursDir,$cityNameDir,$loadToDB,$conn);
	fwrite($writeLogs,date("H:i:s")." - after loadHoursPerCity\r\n");

	if($isOk === false){
		return array($cityId,"Failed to load open hours"); // return error
	}	
	
	
	//optimize the dish table (just in case many data was inserted and index should be reuilt)
	optimizeDishTable($conn);

	

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

	// check if rate_limit_exceeded
function isLimitExceeded($foursquare){
	return $foursquare->rate_limit_exceeded;
}


?>
