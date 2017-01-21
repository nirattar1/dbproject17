<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");
require_once("addValuesToTables.php");


function searchHoursAndMenusPerCity($conn,$foursquare,$jsonsDir,$cityNameDir,$userRequest=false){
	if($userRequest)
		userFeedback("Getting open hours for restaurants");
	searchHoursPerCity($conn,$foursquare,$jsonsDir,$cityNameDir);
	if($userRequest)
		userFeedback("Getting dishes");
	searchMenusPerCity($conn,$foursquare,$jsonsDir,$cityNameDir);
}

// hours
function searchHoursPerCity($conn,$foursquare,$jsonsDir,$cityNameDir){
	$cityId = getCityIdByName($conn,str_replace('_',' ',$cityNameDir));
	$venuesArr = getVenuesArrFromDB($conn,$cityId);
	searchMenusOrHoursByVenueArr($venuesArr,$foursquare,$jsonsDir,'hours/','hours',$cityNameDir);
}

// menus
function searchMenusPerCity($conn,$foursquare,$jsonsDir,$cityNameDir){
	// we'll do requests only for venues that hes_menu==1
	$cityId = getCityIdByName($conn,str_replace('_',' ',$cityNameDir));
	$venuesArr = getVenuesWithMenusArrFromDB($conn,$cityId);
	searchMenusOrHoursByVenueArr($venuesArr,$foursquare,$jsonsDir,'menus/','menu',$cityNameDir);
}

// mutual
function searchMenusOrHoursByVenueArr($venuesArr,$foursquare,$jsonsDir,$menusHoursDir,$type,$cityNameDir){
	$outputPerCity = $jsonsDir.$menusHoursDir.$cityNameDir.'/';
	if(!in_array($cityNameDir,scandir($jsonsDir.$menusHoursDir)))
		mkdir($outputPerCity);
	
	$outputPerCityArr = array_flip(scandir($outputPerCity));
	foreach($venuesArr as $venueId){
		requestForVenue($foursquare,$venueId,$type,$outputPerCity,$outputPerCityArr);
	}
}

// request
function requestForVenue($foursquare,$venueId,$type,$outputDirPerCity,$outputPerCityArr){
	
	$requestType = "venues/$venueId/".$type;
	$params = array();
	$nameParams = array($venueId);
	
	// request api only if not exists
	$fileName = createFileNameByParams($nameParams);
	
	if(!array_key_exists($fileName,$outputPerCityArr)){
		getAndSaveJSON($foursquare,$requestType,$params,$fileName,$outputDirPerCity);
	}
}

// this used for adding new city
function userFeedback($str){
	echo "<h1>$str:</h1><br>";
}

?>
