<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("php-foursquare-master/src/FoursquareApi.php");
require_once("0_functions.php");



function searchHoursAndMenusPerCity($conn,$cityNameDir){
	$foursquare = createNewFoursqaure('2');
	searchHoursPerCity($conn,$cityNameDir);
	searchMenusPerCity($conn,$cityNameDir);
}

// hours
function searchHoursPerCity($conn,$cityNameDir){
	$cityId = getCityIdByName($conn,str_replace('_',' ',$cityNameDir));
	$venuesArr = getVenuesArrFromDB($conn,$cityId);
	searchMenusOrHoursByVenueArr($venuesArr,$foursquare,$jsonsDir,'menus/',$cityNameDir);
}

// menus
function searchMenusPerCity($conn,$cityNameDir){
	// we'll do requests only for venues that hes_menu==1
	$cityId = getCityIdByName($conn,str_replace('_',' ',$cityNameDir));
	$venuesArr = getVenuesWithMenusArrFromDB($conn,$cityId);
	searchMenusOrHoursByVenueArr($venuesArr,$foursquare,$jsonsDir,'menus/',$cityNameDir);
}

// mutual
function searchMenusOrHoursByVenueArr($venuesArr,$foursquare,$jsonsDir,$menusHoursDir,$cityNameDir){
	$outputPerCity = $jsonsDir.$menusHoursDir.$cityNameDir.'/';
	$outputPerCityArr = array_flip(scandir($outputPerCity));
	if(!in_array($cityNameDir,scandir($jsonsDir.$menusHoursDir)))
	if(!in_array($cityNameDir,scandir($jsonsDir.$menusHoursDir)))
		mkdir($outputPerCity);
	
	foreach($venuesArr as $venueId){
		requestForVenue($foursquare,$venueId,"menu",$outputPerCity,$outputPerCityArr);
	}
}

// request
function requestForVenue($foursquare,$venueId,$type,$outputDirPerCity,$outputPerCityArr){
	//foreach($vanuesArrPerCity as $id=>$s){
		
	$requestType = "venues/$venueId/".$type;
	$params = array();
	$nameParams = array($venueId);
	
	// request api only if not exists
	$fileName = createFileNameByParams($nameParams);
	
	if(!array_key_exists($fileName,$outputPerCityArr)){
		getAndSaveJSON($foursquare,$requestType,$params,$fileName,$outputDirPerCity);
	}else{
		copy($outputDirPerCityOld.$fileName,$outputDirPerCity.$fileName);
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
