<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");

// input
$delta = 0.002275; // about 250 meters
//$delta = 0.036117; // about 4 km - for test
//$delta = 0.090300; // about 10 km - for test

//$inputDir = 'input/';
$inputDir = 'input/';
$citiesInputFile = $inputDir."citiesInput.txt";
$requestsOutputFile = "requests.txt";
$failsOutputFile = "failed_requests.txt";



// Set your client key and secret
//$client_key = "3ZGILD2SYIGKM4NVBRIG4AWODIU4TUR02BEOCN21NDXIQNP1"; // this was the auoth_key
$client_key = "PNQBKVKJSRGNN4NXJGMU2J2X1OXWLGM2W5ARZDYDAPUXEJWN";
$client_secret = "CXUSCYJ14XAMKCNYQFLQ2LB45HCAORYHQKDENQQTGGEGJMTB";
$googleApiKey = "AIzaSyDutGO-yGZstF2N3IjGOUv8kWYWi9aGGGk";

// request

// Load the Foursquare API library
if($client_key=="" or $client_secret=="")
{
	echo 'Load client key and client secret from <a href="https://developer.foursquare.com/">foursquare</a>';
	exit;
}


$foursquare = new FoursquareApi($client_key,$client_secret,$requestsOutputFile,$failsOutputFile);


$city2idArr = getCity2idArr($citiesInputFile);
foreach($city2idArr as $cityName=>$cityId){
	$boundingBox = $foursquare->getBoundingBox($cityName,$googleApiKey);
	if($boundingBox==null){
		echo "<br>TODO: bad boundingBox for $cityName<br>";
		exit;
	}
		
	print_r($boundingBox);
	
	// TODO: only one time to put in DB: cityId,cityName,boundingBox-details
	
	$requestType = "venues/search";
	requestCityFunc($foursquare,$cityName,$boundingBox,$requestType,$jsonsDir.$venuesDir,$delta);
}	
exit;


function doWeNeedThis(){
	$venues = json_decode($response);

	foreach($venues->response->venues as $venue){
		if(isset($venue->categories['0']))
		{
			echo '<image class="icon" src="'.$venue->categories['0']->icon->prefix.'88.png"/>';
		}
		else
			echo '<image class="icon" src="https://foursquare.com/img/categories/building/default_88.png"/>';
		echo '<a href="https://foursquare.com/v/'.$venue->id.'" target="_blank"/><b>';
		echo $venue->name;
		echo "</b></a><br/>";
		
		
			
		if(isset($venue->categories['0']))
		{
			if(property_exists($venue->categories['0'],"name"))
			{
				echo ' <i> '.$venue->categories['0']->name.'</i><br/>';
			}
		}
		
		if(property_exists($venue->hereNow,"count"))
		{
				echo ''.$venue->hereNow->count ." people currently here <br/> ";
		}

		echo '<b><i>History</i></b> :'.$venue->stats->usersCount." visitors , ".$venue->stats->checkinsCount." visits ";
	}
}


?>
