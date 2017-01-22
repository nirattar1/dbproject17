<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");
require_once("addValuesToTables.php");

// input
$runForAllCities = 0;

//(only one time - controlled by flag $runForAllCities)
if($runForAllCities){
	$loadToDB = 1; // otherwise - goes to csv for testing

	// build array with values to insert by this array
	$titleToIndex = array('cityId'=>0,'id'=>1,'name'=>2,'url'=>3,'hasMenu'=>4,'phone'=>5,
						'address'=>6,'city'=>7,'state'=>8,'country'=>9,
						'category'=>10,'checkinsCount'=>11,'usersCount'=>12,'tipCount'=>13);


	if($loadToDB){
		// create connection to DB
		$conn = createConnection();
	}else{	
		// for testing before loading to the DB
		
		$citiesInputFile = $inputDir."citiesInput.txt";
		$city2idArr = getCity2idArr($citiesInputFile);
		
		$space = "\r\n";			
		$writeFileName = $csvDir."venues_new_17_01_17.csv";
		$write = fopen($writeFileName,'w');
		fwrite($write,implode(',',array_keys($titleToIndex)).$space);
	}

	// run loadVenuesPerCity for every city
	foreach(scandir($jsonsDir.$venuesDir) as $cityNameDir){
		if($cityNameDir==='.' || $cityNameDir==='..')
			continue;
		
		if($loadToDB){
			$cityId = getCityIdByName($conn,str_replace('_',' ',$cityNameDir));
			if($cityId===FALSE){
				continue;
			}
		}else{
			$cityId = $city2idArr[str_replace('_',' ',$cityNameDir)];
			$conn = 0; //won't be used in this case
		}
		
		loadVenuesPerCity($jsonsDir,$venuesDir,$cityNameDir,$cityId,$loadToDB,$conn);
	}

	// close connection to DB
	if($loadToDB){
		closeConnection($conn);
	}else{
		fclose($write);
	}
}

// parse jsons and upload the relevant fields to uor DB 
function loadVenuesPerCity($jsonsDir,$venuesDir,$cityNameDir,$cityId,$loadToDB,$conn,$write=null){
	// build array with values to insert by this array
	$titleToIndex = array('cityId'=>0,'id'=>1,'name'=>2,'url'=>3,'hasMenu'=>4,'phone'=>5,
				'address'=>6,'city'=>7,'state'=>8,'country'=>9,
				'category'=>10,'checkinsCount'=>11,'usersCount'=>12,'tipCount'=>13);

	// for error handling
	$enteredNum = 0;
	$attemptedToEnter = 0;
	
	foreach(scandir($jsonsDir.$venuesDir.$cityNameDir) as $fileName){
		
		if(strpos($fileName,'.json')===false)
			continue;

		$full_filename = $jsonsDir.$venuesDir.$cityNameDir.'/'.$fileName;
		$jsonStr = file_get_contents($full_filename);
		$jsonArr = json_decode($jsonStr,true);

		// convert json to indexed array and to line in csv / entry in DB
		foreach($jsonArr['response']['venues'] as $i=>$venueDetails){
			$venueArr = venueJson2indexedArr($venueDetails,$titleToIndex,$loadToDB); // indexed array that its values are sorted in the order of $titleToIndex
			$venueArr[$titleToIndex['cityId']] = $cityId;
			
			$attemptedToEnter++;
			if($loadToDB){
				// one entry for each restaurant
				// $isOk is for error handling
				$isOk = addEntryToRestaurantTable($conn,$venueArr,$titleToIndex);
				if($isOk)
					$enteredNum++;
			}else{
				// one row for each restaurant
				fwrite($write,implode(',',$venueArr)."\r\n");
			}
		}
	}
	
	// we want at least 75% of the venues downloaded to be in our DB
	return ($enteredNum/$attemptedToEnter>=0.75); // will be used in case that $loadToDB=1 to see if the city was loaded well
}

?>