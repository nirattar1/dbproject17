<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");
require_once("addValuesToTables.php");


// input
$inputDir = 'input/';
$loadToDB = 1; // otherwise - goes to csv
$writeVenuesWithMenuMode = 1;

// parse
$titleToIndex = array('cityId'=>0,'id'=>1,'name'=>2,'url'=>3,'hasMenu'=>4,'phone'=>5,
					'address'=>6,'city'=>7,'state'=>8,'country'=>9,
					'category'=>10,'checkinsCount'=>11,'usersCount'=>12,'tipCount'=>13);


if($loadToDB){
	$conn = createConnection();
}else{	
	$citiesInputFile = $inputDir."citiesInput.txt";
	$city2idArr = getCity2idArr($citiesInputFile);
	
	$space = "\r\n";			
	$writeFileName = $csvDir."venues_new_17_01_17.csv";
	$write = fopen($writeFileName,'w');
	fwrite($write,implode(',',array_keys($titleToIndex)).$space);
	
	if($writeVenuesWithMenuMode)
		$writeVenuesWithMenu = fopen($inputDir."VenuesWithMenus.txt",'w');
}


foreach(scandir($jsonsDir.$venuesDir) as $cityNameDir){
	if($cityNameDir==='.' || $cityNameDir==='..')
		continue;
	
	if($loadToDB){
		$cityId = getCityIdByName($conn,str_replace('_',' ',$cityNameDir));
		echo "$cityNameDir=$cityId<br>";
		if($cityId===FALSE){
			echo "cityName $cityNameDir wasn't found<br>";
			continue;
		}
	}else{
		$cityId = $city2idArr[str_replace('_',' ',$cityNameDir)];
		$conn = 0;//won't be used in this case
	}
	
	loadVenuesPerCity($jsonsDir,$venuesDir,$cityNameDir,$cityId,$loadToDB,$conn);//$write,$writeVenuesWithMenuMode);
}

if($loadToDB){
	closeConnection($conn);
}else{
	fclose($write);
}

exit;


function loadVenuesPerCity($jsonsDir,$venuesDir,$cityNameDir,$cityId,$loadToDB,$conn,$write=null,$writeVenuesWithMenuMode=false){
	$titleToIndex = array('cityId'=>0,'id'=>1,'name'=>2,'url'=>3,'hasMenu'=>4,'phone'=>5,
				'address'=>6,'city'=>7,'state'=>8,'country'=>9,
				'category'=>10,'checkinsCount'=>11,'usersCount'=>12,'tipCount'=>13);

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
				fwrite($write,implode(',',$venueArr)."\r\n");
			}
		}
	}
}

?>