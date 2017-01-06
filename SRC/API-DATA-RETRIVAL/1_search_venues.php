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
$requestCities = 1;
$parseVenues = 0;


// Set your client key and secret
//$client_key = "3ZGILD2SYIGKM4NVBRIG4AWODIU4TUR02BEOCN21NDXIQNP1"; // this was the auoth_key
$client_key = "PNQBKVKJSRGNN4NXJGMU2J2X1OXWLGM2W5ARZDYDAPUXEJWN";
$client_secret = "CXUSCYJ14XAMKCNYQFLQ2LB45HCAORYHQKDENQQTGGEGJMTB";
$googleApiKey = "AIzaSyDutGO-yGZstF2N3IjGOUv8kWYWi9aGGGk";

// request
if($requestCities){

	// Load the Foursquare API library
	if($client_key=="" or $client_secret=="")
	{
		echo 'Load client key and client secret from <a href="https://developer.foursquare.com/">foursquare</a>';
		exit;
	}

	
	$foursquare = new FoursquareApi($client_key,$client_secret);
	requestCityFuncTest($foursquare,"venues/search",$jsonsDir.$venuesDir,$delta);
	echo "end of test";
	exit;
	
	// "https://maps.googleapis.com/maps/api/geocode/json?address=ney+york&key=
	
	$read = fopen($citiesInputFile,'r') or die ("can't open file");
	while(!feof($read)){
		$cityName = trim(fgets($read));
		$boundingBox = $foursquare->getBoundingBox($cityName,$googleApiKey);
		
		echo $cityName."<br><br>";
		print_r($boundingBox);
		echo "<br>";
		$requestType = "venues/search";
		requestCityFunc($foursquare,$cityName,$boundingBox,$requestType,$jsonsDir.$venuesDir,$delta);
	}	
}

// parse
if($parseVenues){
	$space = "\r\n";
	$titleToIndex = array('id'=>0,'name'=>1,'url'=>2,'hasMenu'=>3,'phone'=>4,
					'address'=>5,'city'=>6,'state'=>7,'country'=>8,'lat'=>9,'lon'=>10,
					'categories'=>11,'checkinsCount'=>12,'usersCount'=>13,'tipCount'=>14,'beenHere'=>15);
					
	$writeFileName = $csvDir.$venuesDir."all.csv";
	$write = fopen($writeFileName,'w');
	fwrite($write,implode(',',array_keys($titleToIndex)).$space);
	
	foreach(scandir($jsonsDir.$venuesDir) as $fileName){
		if(strpos($fileName,'.json')===false)
			continue;
		
		$jsonStr = file_get_contents($jsonsDir.$venuesDir.$fileName);
		
		$jsonArr = json_decode($jsonStr,true);
		// TODO: make sure that the json i valid
		
		$writeFileName = $csvDir.$venuesDir.str_replace('.json','.csv',$fileName);
		
		
		$arrToWrite = array();
		foreach($jsonArr['response']['venues'] as $i=>$venueDetails){ // convert venue json to indexed array and to line in csv
			$indexedArr = venueJson2indexedArr($venueDetails,$titleToIndex);
			
			// write
			fwrite($write,implode(',',array_values($indexedArr)).$space);
		}	
	}
}

function json2csv($array,$write){
		
	$firstLineKeys = false;
	foreach ($array as $line)
	{
		if (empty($firstLineKeys))
		{
			$firstLineKeys = array_keys($line);
			fputcsv($write, $firstLineKeys);
			$firstLineKeys = array_flip($firstLineKeys);
		}
		$line_array = array($line['type']);
		foreach ($line['conversion'] as $value)
		{
			array_push($line_array,$value);
		}
		array_push($line_array,$line['stream_type']);
		fputcsv($write, $line_array);

	}
}



// Generate a latitude/longitude pair using Google Maps API
//list($lat,$lon) = $foursquare->GeoLocate($location);




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

function createFileNameByParams($params){
	$fileName = '';
	foreach($params as $key=>$val){
		$fileName = $fileName.$val.',';
	}
	return substr($fileName,0,strlen($fileName)-1).".json";
}



function fstRow2IndexArr($line,$delimiter = ','){
	$arr = array();
	$parts = explode($delimiter,$line);
	foreach($parts as $i=>$title){		
		$arr[str_replace('"','',$title)] = $i;
	}
	return $arr;
}


?>