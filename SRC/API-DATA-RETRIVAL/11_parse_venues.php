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
$loadToDB = 1; // otherwise - goes to csv
$writeVenuesWithMenuMode = 1;

// parse

$titleToIndex = array('cityId'=>0,'id'=>1,'name'=>2,'url'=>3,'hasMenu'=>4,'phone'=>5,
				'address'=>6,'city'=>7,'state'=>8,'country'=>9,'lat'=>10,'lon'=>11,
				'categories'=>12,'checkinsCount'=>13,'usersCount'=>14,'tipCount'=>15);

$city2idArr = getCity2idArr($citiesInputFile);

if($loadToDB){
	$conn = createConnection();
	//TODO
	//$conn->query("DELETE FROM Restaurant");
	//exit;
}else{	
	$space = "\r\n";			
	$writeFileName = $csvDir.$venuesDir."14_01_17.csv";
	$write = fopen($writeFileName,'w');
	fwrite($write,implode(',',array_keys($titleToIndex)).$space);
	
	if($writeVenuesWithMenuMode)
		$writeVenuesWithMenu = fopen($inputDir."VenuesWithMenus.txt",'w');
}


$cnt2skip = 0; // TODO: delete this
foreach(scandir($jsonsDir.$venuesDir) as $cityName){
	if($cityName==='.' || $cityName==='..')
		continue;
	
	$cityId = $city2idArr[str_replace('_',' ',$cityName)];
	// checking that the city exists in th DB. if not - continue to next city
	if($loadToDB && !cityAlreadyInTable($conn,$cityId))
		continue;
	
	foreach(scandir($jsonsDir.$venuesDir.$cityName) as $fileName){
		
		if(strpos($fileName,'.json')===false)
			continue;
		
		//TODO: delete this
		$sparseConst = (in_array($cityName,array('Boston','Detroit','Phoenix','San_Diego'))? 2 :4);
		if(($cnt2skip++)%$sparseConst) // TODO: delete this // insersts every 4th (or every 2nd in the newer cities)
			continue;

		$full_filename = $jsonsDir.$venuesDir.$cityName.'/'.$fileName;
		//echo $full_filename;
		$jsonStr = file_get_contents($full_filename);

		$jsonArr = json_decode($jsonStr,true);

		foreach($jsonArr['response']['venues'] as $i=>$venueDetails){ // convert venue json to indexed array and to line in csv
			$VenueArr = venueJson2indexedArr($venueDetails,$titleToIndex,$loadToDB);// $loadToDB will control the "" protection
			$VenueArr[$titleToIndex['cityId']] = $cityId;
			
			
			if($loadToDB){
				addEntryToRestaurantTable($conn,$VenueArr,$titleToIndex);
			}else{
				// write
				fwrite($write,implode(',',array_values($VenueArr)).$space);
				if($writeVenuesWithMenuMode && $VenueArr[$titleToIndex['hasMenu']]===1)
					fwrite($writeVenuesWithMenu,$cityName.','.$VenueArr[$titleToIndex['id']].$space);
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
