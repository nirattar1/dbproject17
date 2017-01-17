<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");
require_once("addValuesToTables.php");

// input
$inputDir = 'input/';
$citiesInputFile = $inputDir."citiesInput.txt";


//two uses to this script:
//1. load cities into cities table (done only once). (will be done when loadToDB==1)
//2. (primary usage) fetch venues data using the API. (when loadToDB==0, default).
// see below addNewCity

$loadToDB = 0;


// request

//$foursquare = new FoursquareApi($client_key,$client_secret,$requestsOutputFile,$failsOutputFile);
$foursquare = createNewFoursqaure('1');

// requesting all venues data
//note: city id is determined by line in input file.
//city2idArr is a mapping between city name to its id.
$city2idArr = getCity2idArr($citiesInputFile);

if ($loadToDB)
	$conn = createConnection();


foreach($city2idArr as $cityName=>$cityId){
	$splitNum = 5;
	$categoryId = "4d4b7105d754a06374d81259"; // main food categoryId
	$requestData = 1;
	$venuesDir = 'venues_new/';
	
	
	addNewCity($foursquare,$cityName,//$cityId,
		$jsonsDir,$venuesDir,$splitNum,$categoryId,$loadToDB,$requestData,$conn);
}

if ($loadToDB)
	closeConnection($conn);

exit;

// -- add new city functions --

// cityName - no underscore
function addNewCity($foursquare,$cityName,//$cityId,
		$jsonsDir,$venuesDir,$splitNum,$categotyId,$loadToDB,$requestData,$conn){
		
	// google API part
	$googleApiKey = "AIzaSyDutGO-yGZstF2N3IjGOUv8kWYWi9aGGGk";
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
	//$cityArr[$titleToIndex['cityId']] = $cityId;
	$cityArr[$titleToIndex['cityName']] = $cityName;
	$cityArr[$titleToIndex['north_lat']] = $boundingBox['north_lat'];
	$cityArr[$titleToIndex['south_lat']] = $boundingBox['south_lat'];
	$cityArr[$titleToIndex['east_lon']]  = $boundingBox['east_lon'];
	$cityArr[$titleToIndex['west_lon']]  = $boundingBox['west_lon'];
	
	// put city in DB: cityId,cityName,boundingBox-details	
	//(only one time - controlled by flag $loadToDB)
	if ($loadToDB){
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


function inUSA($boundingBox){
	// check that the boundingBox is inside of USA's boundingBox
	$a = $boundingBox['north_lat'] < 49.38;
	$b = $boundingBox['south_lat'] > 25.82;
	$c = $boundingBox['east_lon'] < -66.94;
	$d = $boundingBox['west_lon'] > -124.39;
	
	return ($a && $b && $c && $d);
}




?>
