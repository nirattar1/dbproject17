<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");
require_once("addValuesToTables.php");

// input
$runForAllCities = 0;

//(only one time - controlled by flag $runForAllCities)
//important note: when running validate that loop ended - otherwise index won't be created.
if($runForAllCities){
	$loadToDB = 1;
	// build array with values to insert by this array
	$titleToIndex = array('venueId'=>0,'sectionName'=>1,'dishId'=>2,'dishName'=>3,'description'=>4,'price'=>5);


	if($loadToDB){
		// create connection to DB
		$conn = createConnection();
	}else{	
		// for testing before loading to the DB

		$space = "\r\n";
		$writeFileName = $csvDir.$menusDir."all.csv";
		$write = fopen($writeFileName,'w');
		fwrite($write,implode(',',array_keys($titleToIndex)).$space);
	}
	
	// run loadMenusPerCity for every city
	foreach(scandir($jsonsDir.$menusDir) as $cityNameDir){
		if($cityNameDir==='.' || $cityNameDir==='..')
			continue;
		
		loadMenusPerCity($jsonsDir,$menusDir,$cityNameDir,$loadToDB,$conn);
	}
		
	//all cities inserted data - time to create index.
	//note: index doesn't need to rebuild on new city
	$result = indexDish($conn);
	
	// close connection to DB
	if($loadToDB){
		closeConnection($conn);
	}else{
		fclose($write);
	}
}


function loadMenusPerCity($jsonsDir,$menusDir,$cityNameDir,$loadToDB,$conn,$write=null){
	// build array with values to insert by this array
	$titleToIndex = array('venueId'=>0,'sectionName'=>1,'dishId'=>2,'dishName'=>3,'description'=>4,'price'=>5);

	foreach(scandir($jsonsDir.$menusDir.$cityNameDir) as $fileName){
		if(strpos($fileName,'.json')===false)
			continue;
		
		
		$jsonStr = file_get_contents($jsonsDir.$menusDir.$cityNameDir.'/'.$fileName);
		$jsonArr = json_decode($jsonStr,true);
		
		$venueId = substr($fileName,0,strpos($fileName,'.'));
		
		// checking that the venue exists in th DB. if not - continue to next menu
		if($loadToDB && !venueAlreadyInTable($conn,$venueId))
			continue;
		
		// skip empty menu
		if($jsonArr['response']['menu']['menus']['count']===0)
			continue;
		
		// for error handling
		$enteredNum = 0;
		$attemptedToEnter = 0;
	
		// convert json to indexed array and to line in csv / entry in DB
		foreach($jsonArr['response']['menu']['menus']['items'] as $i=>$oneMenu){ 
			foreach($oneMenu['entries']['items'] as $j=>$itemSection){ // section in the menu: salads, sandwiches, desserts
				// menu section (like "Sandwiches")
				$sectionName = getFieldOrNull($itemSection,'name',1,$loadToDB); // we must have this field, some times dish names can't be understood without the section name
				
				foreach($itemSection['entries']['items'] as $k=>$dishDetails){
					$indexedArr = dishJson2indexedArr($dishDetails,$titleToIndex,$loadToDB);  // indexed array that its values are sorted in the order of $titleToIndex
					
					$indexedArr[$titleToIndex['venueId']] = $venueId;
					$indexedArr[$titleToIndex['sectionName']] = $sectionName;

					if($indexedArr[$titleToIndex['price']] === null)// we don't add dishes without price
						continue;
					
					$attemptedToEnter++;
					if($loadToDB){
						// one entry for each dish
						$isOk = addEntryToDishTable($conn,$indexedArr,$titleToIndex);
						if($isOk)
							$enteredNum++;
					}else{
						// one row for each dish
						fwrite($write,implode(',',array_values($indexedArr)).$space);
					}
				}
				
			}	
		}	
	}

	// we want at least 75% of the venues downloaded to be in our DB
	return ($enteredNum/$attemptedToEnter>=0.75); // will be used in case that $loadToDB=1 to see if the city was loaded well
}


?>
