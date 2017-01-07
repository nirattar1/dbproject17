<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");
require_once("addValuesToTables.php");

// input
$delta = 0.002275; // about 250 meters
//$delta = 0.036117; // about 4 km - for test
//$delta = 0.090300; // about 10 km - for test

//$inputDir = 'input/';
$inputDir = 'input/';
$citiesInputFile = $inputDir."citiesInput.txt";
$loadToDB = 1; // otherwise - goes to csv


// parse
if($loadToDB){
	$conn = createConnection();
	
}else{	
	$space = "\r\n";			
	$writeFileName = $csvDir.$venuesDir."07_01_17.csv";
	$write = fopen($writeFileName,'w');
	fwrite($write,implode(',',array_keys($titleToIndex)).$space);
}

$space = "\r\n";
$titleToIndex = array('cityId'=>0,'id'=>1,'name'=>2,'url'=>3,'hasMenu'=>4,'phone'=>5,
				'address'=>6,'city'=>7,'state'=>8,'country'=>9,'lat'=>10,'lon'=>11,
				'categories'=>12,'checkinsCount'=>13,'usersCount'=>14,'tipCount'=>15);

$city2idArr = getCity2idArr($citiesInputFile);

foreach(scandir($jsonsDir.$venuesDir) as $fileName){
	if(strpos($fileName,'.json')===false)
		continue;
	
	$jsonStr = file_get_contents($jsonsDir.$venuesDir.$fileName);
	
	$jsonArr = json_decode($jsonStr,true);
	$cityName = str_replace('_',' ',substr($fileName,0,strpos($fileName,',')));
	$cityId = $city2idArr[$cityName];
	
	$arrToWrite = array();
	foreach($jsonArr['response']['venues'] as $i=>$venueDetails){ // convert venue json to indexed array and to line in csv
		$VenueArr = venueJson2indexedArr($venueDetails,$titleToIndex);
		$VenueArr[$titleToIndex['cityId']] = $cityId;
		
		if($loadToDB){
			addEntryToRestaurantTable($conn,$VenueArr,$titleToIndex);
			//TODO: remove after test
			closeConnection($conn);
			exit;
			
		}else{
			// write
			fwrite($write,implode(',',array_values($VenueArr)).$space);
		}
	}	
}

if($loadToDB){
	closeConnection($conn);
}else{
	fclose($write);
}

exit;


?>
