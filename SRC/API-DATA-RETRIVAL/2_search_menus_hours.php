<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("php-foursquare-master/src/FoursquareApi.php");
require_once("0_functions.php");

$inputDir = 'input/';

$foursquare = createNewFoursqaure('2');

function searchHoursAndMenusPerCity($cityName){
	
}


function searchHoursPerCity($conn,$cityName){
	$cityId = getCityIdByName($conn,str_replace('_',' ',$cityName));
	$venuesArr = getVenuesWithMenusArrFromDB($conn,$cityId);
	searchMenusOrHoursByVenueArr($venuesArr,$foursquare,$jsonsDir,'menus/',$cityName);
}

function searchMenusPerCity($conn,$cityName){
	// we'll do requests only for venues that hes_menu==1
	$cityId = getCityIdByName($conn,str_replace('_',' ',$cityName));
	$venuesArr = getVenuesWithMenusArrFromDB($conn,$cityId);
	searchMenusOrHoursByVenueArr($venuesArr,$foursquare,$jsonsDir,'menus/',$cityName);
}


//$venuesWithMenusArr = getVenuesWithMenusArr($inputDir."VenuesWithMenus.txt"); // read the input from 11_parse_venues.php
//foreach($venuesWithMenusArr as $cityName=>$venuesArr){
//	searchMenusHoursPerCity($foursquare,$jsonsDir,$menusDir,$hoursDir,$cityName);
//}
exit;


function searchMenusOrHoursByVenueArr($venuesArr,$foursquare,$jsonsDir,$menusHoursDir,$cityName){
	$outputPerCity = $jsonsDir.$menusHoursDir.$cityName.'/';
	$outputPerCityArr = array_flip(scandir($outputPerCity));
	if(!in_array($cityName,scandir($jsonsDir.$menusHoursDir)))
		mkdir($outputPerCity);
	
	foreach($venuesArr as $venueId){
		requestForVenue($foursquare,$venueId,"menu",$outputPerCity,$outputPerCityArr);
	}
}


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
		//echo "copied $fileName from $outputDirPerCityOld to $outputDirPerCity<br>";
	}
}


?>
