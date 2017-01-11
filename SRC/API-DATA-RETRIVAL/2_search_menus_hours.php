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
	$outputMenusPerCity = $jsonsDir.$menusDir.$cityName.'/';
	if(!in_array($cityName,scandir($jsonsDir.$menusDir)))
		mkdir($outputMenusPerCity);
	
	// hours
	$outputHoursPerCity = $jsonsDir.$hoursDir.$cityName.'/';
	if(!in_array($cityName,scandir($jsonsDir.$hoursDir)))
		mkdir($outputHoursPerCity);
	
	foreach($venuesArr as $venueId){
		// menus
		$outputMenusPerCityArr = array_flip(scandir($outputMenusPerCity));
		requestForVenue($foursquare,$venueId,"menu",$outputMenusPerCity,$outputMenusPerCityArr);
		
		// hours
		$outputHoursPerCityArr = array_flip(scandir($outputHoursPerCity));
		requestForVenue($foursquare,$venueId,"hours",$outputHoursPerCity,$outputHoursPerCityArr);
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

function requestForVenue($foursquare,$venueId,$type,$outputDirPerCity,$outputPerCityArr){
	//foreach($vanuesArrPerCity as $id=>$s){
		
	$requestType = "venues/$venueId/".$type;
	$params = array();
	$nameParams = array($venueId);
	
	// request api only if not exists
	$fileName = createFileNameByParams($nameParams);
	
	if(!array_key_exists($fileName,$outputPerCityArr))
		getAndSaveJSON($foursquare,$requestType,$params,$fileName,$outputDirPerCity);
}



// to delete?
foreach(scandir($jsonsDir.$venuesDir) as $cityName){
	if($cityName==='.' || $cityName==='..')
		continue;
	
	$vanuesArrPerCity = array();
	foreach(scandir($jsonsDir.$venuesDir.$cityName) as $fileName){
		
		if(strpos($fileName,'.json')===false)
			continue;
	
		$jsonStr = file_get_contents($jsonsDir.$venuesDir.$cityName.'/'.$fileName);
		$jsonArr = json_decode($jsonStr,true);
		// TODO: make sure that the json i valid
		
		//print_r($jsonArr['response']['venues']);
		
		echo "fileName=$fileName<br>".sizeof($jsonArr['response']['venues'])."<br>";
		
		foreach($jsonArr['response']['venues'] as $i=>$venueDetails){
			//TODO: if has menu
			$id = $venueDetails['id'];
			$vanuesArrPerCity[$id] = 0;
		}
	}
	
	// menus
	$outputMenusPerCity = $jsonsDir.$menusDir.$cityName;
	$outputMenusPerCityArr = array_flip(scandir($outputMenusPerCity));
	requestForCityVenues($vanuesArrPerCity,"menu",$outputMenusPerCity,$outputMenusPerCityArr);
	
	// hours
	$outputHoursPerCity = $jsonsDir.$hoursDir.$cityName;
	$outputHoursPerCityArr = array_flip(scandir($outputMenusPerCity));
	requestForCityVenues($vanuesArrPerCity,"hours",$outputHoursPerCity,$outputHoursPerCityArr);
}




?>
