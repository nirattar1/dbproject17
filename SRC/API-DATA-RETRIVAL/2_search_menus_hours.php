<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");
require_once("addValuesToTables.php");

// do api requests of open hours and dishes
// returns true if everything went good, otherwise - returns string (error)
function searchHoursAndMenusPerCity($conn,$foursquare,$jsonsDir,$cityNameDir){
	$wentGood = searchHoursPerCity($conn,$foursquare,$jsonsDir,$cityNameDir);
	if(! $wentGood) // error
		return "Something went wrong with getting open hours for restaurants";
	
	$wentGood = searchMenusPerCity($conn,$foursquare,$jsonsDir,$cityNameDir);
	if(! $wentGood) // error
		return "Something went wrong with getting dishes";

	return true; // success!
}

// hours
function searchHoursPerCity($conn,$foursquare,$jsonsDir,$cityNameDir){
	$cityId = getCityIdByName($conn,str_replace('_',' ',$cityNameDir));
	$venuesArr = getVenuesArrFromDB($conn,$cityId);
	return searchMenusOrHoursByVenueArr($venuesArr,$foursquare,$jsonsDir,'hours/','hours',$cityNameDir);
}

// menus
function searchMenusPerCity($conn,$foursquare,$jsonsDir,$cityNameDir){
	// we'll do requests only for venues that hes_menu==1
	$cityId = getCityIdByName($conn,str_replace('_',' ',$cityNameDir));
	$venuesArr = getVenuesWithMenusArrFromDB($conn,$cityId);
	return searchMenusOrHoursByVenueArr($venuesArr,$foursquare,$jsonsDir,'menus/','menu',$cityNameDir);
}

// mutual
function searchMenusOrHoursByVenueArr($venuesArr,$foursquare,$jsonsDir,$menusHoursDir,$type,$cityNameDir){
	$requestsNum = 0;
	
	// create directory for the results
	$outputPerCity = $jsonsDir.$menusHoursDir.$cityNameDir.'/';
	if(!in_array($cityNameDir,scandir($jsonsDir.$menusHoursDir)))
		mkdir($outputPerCity);
	
	// make the FS api requests
	$outputPerCityArr = array_flip(scandir($outputPerCity));
	foreach($venuesArr as $venueId){
		$isGoodResponse = requestForVenue($foursquare,$venueId,$type,$outputPerCity,$outputPerCityArr);
		if($isGoodResponse)
			$requestsNum++;
	}
	
	// search was good if at least half of the requests were good
	if($requestsNum>=sizeof($venuesArr))
		return true;
	else
		return false;
}

// request
// returns false if request had failed. otherwise - true
function requestForVenue($foursquare,$venueId,$type,$outputDirPerCity,$outputPerCityArr){
	
	$requestType = "venues/$venueId/".$type;
	$params = array();
	$nameParams = array($venueId);
	
	// request api only if not exists
	$fileName = createFileNameByParams($nameParams);
	
	if(!array_key_exists($fileName,$outputPerCityArr)){
		$isGoodResponse = getAndSaveJSON($foursquare,$requestType,$params,$fileName,$outputDirPerCity);
		if(!$isGoodResponse)
			return false; // bad response
	}
	
	return true;
}

?>
