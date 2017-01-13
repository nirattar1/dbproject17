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

$loadToDB = 1;

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

print_r($city2idArr);

foreach($city2idArr as $cityName=>$cityId){
	$splitNum = 30;
	$categoryId = "4d4b7105d754a06374d81259";
	addNewCity($foursquare,$googleApiKey,$cityName,$cityId,
		$jsonsDir,$venuesDir,$splitNum,$categoryId,$loadToDB);
}


function addNewCity($foursquare,$googleApiKey,$cityName,$cityId,
<<<<<<< HEAD
		$jsonsDir,$venuesDir,$splitNum,$categotyId,$loadToDB)	
{

=======
		$jsonsDir,$venuesDir,$splitNum,$categotyId, $loadToDB)		
{
>>>>>>> 53b00e46c4ac129f2310cb5a99ad23897a39c507
	$boundingBox = $foursquare->getBoundingBox($cityName,$googleApiKey);
	if($boundingBox==null){
		echo "<br>TODO: bad boundingBox for $cityName<br>";
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
		return 0; // just in loading
	}	
	
	//assume city already exists in db. move on to API.
	
	$requestType = "venues/search";
	$cityNameDir = str_replace(' ','_',$cityName).'/';
	$outputDir = $jsonsDir.$venuesDir.$cityNameDir;
	
	if(!in_array(str_replace('/','',$cityNameDir),scanDir($jsonsDir.$venuesDir))){
		mkdir($outputDir);
		if($cityNameDir=='San_Francisco')
			echo "create new directory for $cityNameDir";
	}
	if($cityNameDir=='San_Francisco')
		exit;
	
	requestCityFunc($foursquare,$cityName,$boundingBox,$requestType,$categotyId,$outputDir,$splitNum);	
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
