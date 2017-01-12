<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");
require_once("addValuesToTables.php");

// input
$delta = 0.002275; // about 250 meters
//$delta = 0.036117; // about 4 km - for test
//$delta = 0.090300; // about 10 km - for test

$inputDir = 'input/';
$citiesInputFile = $inputDir."citiesInput.txt";
$loadToDB = 0; // otherwise - goes to csv
$writeVenuesWithMenuMode = 1;

// parse

$titleToIndex = array('cityId'=>0,'id'=>1,'name'=>2,'url'=>3,'hasMenu'=>4,'phone'=>5,
				'address'=>6,'city'=>7,'state'=>8,'country'=>9,'lat'=>10,'lon'=>11,
				'categories'=>12,'checkinsCount'=>13,'usersCount'=>14,'tipCount'=>15);

$city2idArr = getCity2idArr($citiesInputFile);

if($loadToDB){
	$conn = createConnection();
	
}else{	
	$space = "\r\n";			
	$writeFileName = $csvDir.$venuesDir."11_01_17.csv";
	$write = fopen($writeFileName,'w');
	fwrite($write,implode(',',array_keys($titleToIndex)).$space);
	
	if($writeVenuesWithMenuMode)
		$writeVenuesWithMenu = fopen($inputDir."VenuesWithMenus.txt",'w');
}

foreach(scandir($jsonsDir.$venuesDir) as $cityName){
	if($cityName==='.' || $cityName==='..')
		continue;
	
	$cityId = $city2idArr[str_replace('_',' ',$cityName)];
	
	foreach(scandir($jsonsDir.$venuesDir.$cityName) as $fileName){
		
		if(strpos($fileName,'.json')===false)
			continue;

		$jsonStr = file_get_contents($jsonsDir.$venuesDir.$cityName.'/'.$fileName);

		$jsonArr = json_decode($jsonStr,true);

		$arrToWrite = array();
		foreach($jsonArr['response']['venues'] as $i=>$venueDetails){ // convert venue json to indexed array and to line in csv
			$VenueArr = venueJson2indexedArr($venueDetails,$titleToIndex,$loadToDB);// $loadToDB will control the "" protection
			$VenueArr[$titleToIndex['cityId']] = $cityId;
			
			if($writeVenuesWithMenuMode && $VenueArr[$titleToIndex['hasMenu']]===1)
				fwrite($writeVenuesWithMenu,$cityName.','.$VenueArr[$titleToIndex['id']].$space);
			
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
}

if($loadToDB){
	closeConnection($conn);
}else{
	fclose($write);
}

exit;


?>
