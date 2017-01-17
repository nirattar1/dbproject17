<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");
require_once("addValuesToTables.php");


$venuesDir = 'venues_new/';

// input
$inputDir = 'input/';
$loadToDB = 0; // otherwise - goes to csv
$writeVenuesWithMenuMode = 1;

// parse
$titleToIndex = array('cityId'=>0,'id'=>1,'name'=>2,'url'=>3,'hasMenu'=>4,'phone'=>5,
				'address'=>6,'city'=>7,'state'=>8,'country'=>9,'lat'=>10,'lon'=>11,
				'category'=>12,'checkinsCount'=>13,'usersCount'=>14,'tipCount'=>15);


if($loadToDB){
	$conn = createConnection();
}else{	
	$citiesInputFile = $inputDir."citiesInput.txt";
	$city2idArr = getCity2idArr($citiesInputFile);
	
	$space = "\r\n";			
	$writeFileName = $csvDir."venues_new_16_01_17.csv";
	$write = fopen($writeFileName,'w');
	fwrite($write,implode(',',array_keys($titleToIndex)).$space);
	
	if($writeVenuesWithMenuMode)
		$writeVenuesWithMenu = fopen($inputDir."VenuesWithMenus.txt",'w');
}


foreach(scandir($jsonsDir.$venuesDir) as $cityNameDir){
	if($cityName==='.' || $cityName==='..')
		continue;
	
	if($loadToDB){
		$cityId = getCityIdByName($conn,str_replace('_',' ',$cityNameDir));
		if($cityId===FALSE){
			echo "cityName $cityName wasn't found<br>";
			continue;
	}else{
		$cityId = $city2idArr[str_replace('_',' ',$cityNameDir)];
	}
	
	loadVenuesPerCity($jsonsDir,$venuesDir,$cityNameDir,$loadToDB,$write,$writeVenuesWithMenuMode);
}

if($loadToDB){
	closeConnection($conn);
}else{
	fclose($write);
}

exit;


function loadVenuesPerCity($jsonsDir,$venuesDir,$cityNameDir,$loadToDB,$conn,$write=null,$writeVenuesWithMenuMode=false){
	$titleToIndex = array('cityId'=>0,'id'=>1,'name'=>2,'url'=>3,'hasMenu'=>4,'phone'=>5,
				'address'=>6,'city'=>7,'state'=>8,'country'=>9,'lat'=>10,'lon'=>11,
				'category'=>12,'checkinsCount'=>13,'usersCount'=>14,'tipCount'=>15);

	foreach(scandir($jsonsDir.$venuesDir.$cityNameDir) as $fileName){
		
		if(strpos($fileName,'.json')===false)
			continue;

		$full_filename = $jsonsDir.$venuesDir.$cityNameDir.'/'.$fileName;
		$jsonStr = file_get_contents($full_filename);
		$jsonArr = json_decode($jsonStr,true);

		foreach($jsonArr['response']['venues'] as $i=>$venueDetails){ // convert venue json to indexed array and to line in csv
			$venueArr = venueJson2indexedArr($venueDetails,$titleToIndex,$loadToDB);// $loadToDB will control the "" protection
			$venueArr[$titleToIndex['cityId']] = $cityId;
			
			
			if($loadToDB){
				addEntryToRestaurantTable($conn,$venueArr,$titleToIndex);
			}else{
				// write
				fwrite($write,implode(',',array_values($venueArr)).$space);
				if($writeVenuesWithMenuMode && $venueArr[$titleToIndex['hasMenu']]===1)
					fwrite($writeVenuesWithMenu,$cityNameDir.','.$venueArr[$titleToIndex['id']].$space);
			}
		}
	}
}

?>
