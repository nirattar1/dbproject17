<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");
require_once("addValuesToTables.php");

// input
$citiesInputFile = $inputDir."citiesInput.txt";

$runForAllCities = 0;

//(only one time - controlled by flag $runForAllCities)
if($runForAllCities){
	$loadToDB = 1;
	$foursquare = createNewFoursqaure('1');

	// requesting all venues data (for the cities we wanted to retrive)
	// determined by the input file.
	$getCitiesToAdd = getCity2idArr($citiesInputFile);

	if ($loadToDB)
		$conn = createConnection();

	foreach($getCitiesToAdd as $cityName){
		$splitNum = 10;
		$categoryId = "4d4b7105d754a06374d81259";
		$requestData = 0;
		
		addNewCity($foursquare,$cityName,
			$jsonsDir,$venuesDir,$splitNum,$categoryId,$loadToDB,$requestData,$conn);
	}

	if ($loadToDB)
		closeConnection($conn);
}

//two uses to this script:
//1. load cities into cities table (done only once). (will be done when loadToDB==1)
//2. (primary usage) search venues data using the API. (when requestData==1).
// returns something og the form array(boolean, "error message")
function addNewCity($foursquare,$cityName, // cityName - no underscore
		$jsonsDir,$venuesDir,$splitNum,$categotyId,$loadToDB,$requestData,$conn){
		
	// google API part
	$googleApiKey = "AIzaSyDutGO-yGZstF2N3IjGOUv8kWYWi9aGGGk";
	$boundingBox = $foursquare->getBoundingBox($cityName,$googleApiKey);
	// null when we have problem in requesting google api
	if($boundingBox === null){
		return array(false,"Something went wrong... Please try again."); // error
	}
	if($boundingBox === 0){
		return array(false,"City wasn't found. Try a different city."); // error
	}
	if(!inUSA($boundingBox)){
		return array(false,"This city is not in the USA"); // error
	}
	if(cityAlreadyInTableBB($conn,$boundingBox)){
		return array(false,"We already have this city"); // error (kind of)
	}
	
	$titleToIndex = array('cityId'=>0,'cityName'=>1,'north_lat'=>2,'south_lat'=>3, 'east_lon'=>4,'west_lon'=>5);
	$cityArr = array_fill(0,sizeof($titleToIndex),'');
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
		return array(true,''); // when already have the data
	
	//assume city already exists in db
	//now do the api requsts
	
	$requestType = "venues/search";
	$cityNameDir = str_replace(' ','_',$cityName).'/';
	$outputDir = $jsonsDir.$venuesDir.$cityNameDir;
	
	if(!in_array(str_replace('/','',$cityNameDir),scanDir($jsonsDir.$venuesDir)))
		mkdir($outputDir);
	
	$requestsNum = requestCityFunc($foursquare,$cityName,$boundingBox,$requestType,$categotyId,$outputDir,$splitNum);
	
	// not enough results for the city
	if($requestsNum<=$splitNum)
		return array(false,"Requesting restaurants has failed. Please try again."); // error
	
	return array(true,'');
}

?>
