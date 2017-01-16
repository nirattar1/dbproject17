<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");
require_once("addValuesToTables.php");

// input
//$delta = 0.002275; // about 250 meters
//$delta = 0.00455;  // about 500 meters
//$delta = 0.036117; // about 4 km - for test
//$delta = 0.090300; // about 10 km - for test

//$inputDir = 'input/';
$inputDir = 'input/';
$citiesInputFile = $inputDir."citiesInput.txt";
$requestsOutputFile = "requests.txt";
$failsOutputFile = "failed_requests.txt";


//two uses to this script:
//1. load cities into cities table (done only once). (will be done when loadToDB==1)
//2. (primary usage) fetch venues data using the API. (when loadToDB==0, default).
// see below addNewCity

$loadToDB = 0;

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


// requesting all venues data
//note: city id is determined by line in input file.
//city2idArr is a mapping between city name to its id.
$city2idArr = getCity2idArr($citiesInputFile);


foreach($city2idArr as $cityName=>$cityId){
	$splitNum = 10;
	$categoryId = "4d4b7105d754a06374d81259";
	$requestData = 1;
	$venuesDir = 'venues_new/';
	
	
	addNewCity($foursquare,$googleApiKey,$cityName,$cityId,
		$jsonsDir,$venuesDir,$splitNum,$categoryId,$loadToDB,$requestData);
}


function addNewCity($foursquare,$googleApiKey,$cityName,$cityId,
		$jsonsDir,$venuesDir,$splitNum,$categotyId,$loadToDB,$requestData){
				
	$boundingBox = $foursquare->getBoundingBox($cityName,$googleApiKey);
	if($boundingBox==null){
		// TODO: something went wrong message
		return 0;
	}
	if(!inUSA($boundingBox)){
		// TODO: seems that this city is not in usa. try different city
		return 0;
	}
	
	$titleToIndex = array('cityId'=>0,'cityName'=>1,'north_lat'=>2,'south_lat'=>3, 'east_lon'=>4,'west_lon'=>5);
	$cityArr = array_fill(0,sizeof($titleToIndex),'');
	$cityArr[$titleToIndex['cityId']] = $cityId;
	$cityArr[$titleToIndex['cityName']] = $cityName;
	$cityArr[$titleToIndex['north_lat']] = $boundingBox['north_lat'];
	$cityArr[$titleToIndex['south_lat']] = $boundingBox['south_lat'];
	$cityArr[$titleToIndex['east_lon']]  = $boundingBox['east_lon'];
	$cityArr[$titleToIndex['west_lon']]  = $boundingBox['west_lon'];
	
	// put city in DB: cityId,cityName,boundingBox-details	
	//(only one time - controlled by flag $loadToDB)
	if ($loadToDB)
	{
		$conn = createConnection();
		addEntryToCityTable($conn, $cityArr, $titleToIndex);
	}
	if(!$requestData)
		return 0; // when already have the data
	
	//assume city already exists in db
	//now do the api requsts
	
	$requestType = "venues/search";
	$cityNameDir = str_replace(' ','_',$cityName).'/';
	$outputDir = $jsonsDir.$venuesDir.$cityNameDir;
	
	if(!in_array(str_replace('/','',$cityNameDir),scanDir($jsonsDir.$venuesDir))){
		mkdir($outputDir);
		if($cityNameDir=='San_Francisco')
			echo "create new directory for $cityNameDir";
	}
	
	requestCityFunc($foursquare,$cityName,$boundingBox,$requestType,$categotyId,$outputDir,$splitNum);	
}
	
exit;


// -- add new city part --

function inUSA($boundingBox){
	// check that the boundingBox is inside of USA's boundingBox
	$a = $boundingBox['north_lat'] < 49.38;
	$b = $boundingBox['south_lat'] > 25.82;
	$c = $boundingBox['east_lon'] < -66.94;
	$d = $boundingBox['west_lon'] > -124.39;
	
	return ($a && $b && $c && $d);
}




?>
