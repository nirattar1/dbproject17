<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("php-foursquare-master/src/FoursquareApi.php");
require_once("0_functions.php");

$inputDir = 'input/';
$requestsOutputFile = "2_requests.txt";
$failsOutputFile = "2_failed_requests.txt";


// Set your client key and secret
//$client_key = "3ZGILD2SYIGKM4NVBRIG4AWODIU4TUR02BEOCN21NDXIQNP1";
$client_key = "PNQBKVKJSRGNN4NXJGMU2J2X1OXWLGM2W5ARZDYDAPUXEJWN";
$client_secret = "CXUSCYJ14XAMKCNYQFLQ2LB45HCAORYHQKDENQQTGGEGJMTB";
// Load the Foursquare API library

if($client_key=="" or $client_secret=="")
{
	echo 'Load client key and client secret from <a href="https://developer.foursquare.com/">foursquare</a>';
	exit;
}

$foursquare = new FoursquareApi($client_key,$client_secret,$requestsOutputFile,$failsOutputFile);
$location = array_key_exists("location",$_GET) ? $_GET['location'] : "Montreal, QC";


$venuesWithMenusArr = getVenuesWithMenusArr($inputDir."VenuesWithMenus.txt"); // read the input from 11_parse_venues.php

foreach($venuesWithMenusArr as $cityName=>$venuesArr){
	
	// menus
	$outputMenusPerCity = $jsonsDir.'menus_new/'.$cityName.'/';
	$outputMenusPerCityOld = $jsonsDir.$menusDir.$cityName.'/';
	//if(!in_array($cityName,scandir($jsonsDir.$menusDir)))
	if(!in_array($cityName,scandir($jsonsDir.'menus_new/')))
		mkdir($outputMenusPerCity);
	
	// hours
	$outputHoursPerCity = $jsonsDir.'hours_new/'.$cityName.'/';
	$outputHoursPerCityOld = $jsonsDir.$hoursDir.$cityName.'/';
	//if(!in_array($cityName,scandir($jsonsDir.$hoursDir)))
	if(!in_array($cityName,scandir($jsonsDir.'hours_new/')))
		mkdir($outputHoursPerCity);
	
	foreach($venuesArr as $venueId){
		// menus
		$outputMenusPerCityArr = array_flip(scandir($outputMenusPerCityOld));
		requestForVenue($foursquare,$venueId,"menu",$outputMenusPerCity,$outputMenusPerCityArr,$outputMenusPerCityOld);
		
		// hours
		$outputHoursPerCityArr = array_flip(scandir($outputHoursPerCityOld));
		requestForVenue($foursquare,$venueId,"hours",$outputHoursPerCity,$outputHoursPerCityArr,$outputHoursPerCityOld);
	}
}
exit;



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

function requestForVenue($foursquare,$venueId,$type,$outputDirPerCity,$outputPerCityArr,$outputDirPerCityOld){ //TODO: delete the last param, it's for fixig
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
