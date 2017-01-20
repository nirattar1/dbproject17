<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");
require_once("addValuesToTables.php");


function searchHoursAndMenusPerCity($conn,$jsonsDir,$cityNameDir){
	$foursquare = createNewFoursqaure('2');
	searchHoursPerCity($conn,$foursquare,$jsonsDir,$cityNameDir);
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
	$outputPerCityArr = array_flip(scandir($outputPerCity));
	if(!in_array($cityNameDir,scandir($jsonsDir.$menusHoursDir)))
		mkdir($outputPerCity);
	
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
		echo "writing $fileName ";
		getAndSaveJSON($foursquare,$requestType,$params,$fileName,$outputDirPerCity);
	}
}


/* non DB way
function getVenuesWithMenusArr($readFileName){
	$venuesWithMenusArr = array();
	$read = fopen($readFileName,'r');
	while(!feof($read)){
		$line = trim(fgets($read));
		if($line==='')
			continue;
		
		list($cityName,$venueId) = explode(',',$line);
		if(!array_key_exists($cityName,$venuesWithMenusArr))
			$venuesWithMenusArr[$cityName] = array();
		$venuesWithMenusArr[$cityName][] = $venueId;
	}
	fclose($read);
	return $venuesWithMenusArr;
}
 */

?>
