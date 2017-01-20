<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");
require_once("addValuesToTables.php");

// input
$citiesInputFile = $inputDir."citiesInput.txt";


//two uses to this script:
//1. load cities into cities table (done only once). (will be done when loadToDB==1)
//2. (primary usage) search venues data using the API. (when requestData==1).
// see below addNewCity

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


function addNewCity($foursquare,$cityName, // cityName - no underscore
		$jsonsDir,$venuesDir,$splitNum,$categotyId,$loadToDB,$requestData,$conn){
		
	// google API part
	$googleApiKey = "AIzaSyDutGO-yGZstF2N3IjGOUv8kWYWi9aGGGk";
	$boundingBox = $foursquare->getBoundingBox($cityName,$googleApiKey);
	if($boundingBox==null){
		// TODO: something went wrong message
		return false;
	}
	if(!inUSA($boundingBox)){
		// TODO: seems that this city is not in usa. try different city
		echo "this city is not in USA<br>";
		return false;
	}
	if(cityAlreadyInTableBB($conn,$boundingBox)){
		// TODO: this city is already in our DB
		echo "We already have this city<br>";
		return false;
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
	
	return true;
}

?>
