<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");
require_once("addValuesToTables.php");

// input
$runForAllCities = 0;

//(only one time - controlled by flag $runForAllCities)
if($runForAllCities){
	$loadToDB = 1;
	// build array with values to insert by this array
	$titleToIndex = array('venueId'=>0,'day'=>1,'start'=>2,'end'=>3);


	if($loadToDB){
		// create connection to DB
		$conn = createConnection();
	}else{	
		// for testing before loading to the DB

		$space = "\r\n";
		$writeFileName = $csvDir.$hoursDir."hours.csv";
		$write = fopen($writeFileName,'w');
		fwrite($write,implode(',',array_keys($titleToIndex)).$space);
	}

	// load open hours foreach city
	foreach(scandir($jsonsDir.$hoursDir) as $cityNameDir){
		if($cityNameDir==='.' || $cityNameDir==='..')
			continue;
		
		loadHoursPerCity($jsonsDir,$hoursDir,$cityNameDir,$loadToDB,$conn);
	}
	
	// close connection to DB
	if($loadToDB){
		closeConnection($conn);
	}else{
		fclose($write);
	}
}


function loadHoursPerCity($jsonsDir,$hoursDir,$cityNameDir,$loadToDB,$conn,$write=null){
	// build array with values to insert by this array
	$titleToIndex = array('venueId'=>0,'day'=>1,'start'=>2,'end'=>3);

	foreach(scandir($jsonsDir.$hoursDir.$cityNameDir) as $fileName){
		if(strpos($fileName,'.json')===false)
			continue;
		
		
		$jsonStr = file_get_contents($jsonsDir.$hoursDir.$cityNameDir.'/'.$fileName);
		$jsonArr = json_decode($jsonStr,true);
		
		$venueId = substr($fileName,0,strpos($fileName,'.'));
		
		// checking that the venue exists in th DB. if not - continue to next menu
		if($loadToDB && !venueAlreadyInTable($conn,$venueId))
			continue;
		
		// skip empty menu
		if(!isset($jsonArr['response']['hours']['timeframes']))
			continue;
		
		// build the array that will be passed to the func addEntryToHoursTable
		$indexedArr = array_fill(0,sizeof($titleToIndex),'');
		$indexedArr[$titleToIndex['venueId']] = $venueId;

		// for error handling
		$enteredNum = 0;
		$attemptedToEnter = 0;
		
		// convert json to indexed array and to line in csv / entry in DB
		foreach($jsonArr['response']['hours']['timeframes'] as $i=>$oneTimeframe){ 
			foreach($oneTimeframe['days'] as $j=>$day){ 
				
				foreach($oneTimeframe['open'] as $k=>$rangeArr){
					list($startFrame,$notRelevant) = formatTime($rangeArr['start']);
					list($endFrame,$isNextDay) = formatTime($rangeArr['end']); // end time can contain '+' - that's when we was to split this range into 2 ranges (current day and next day) 
					
					$maybeSplitedRanges = splitRangeIfNeeded($startFrame,$endFrame,$isNextDay,$day); // if need to split - it will be array with 2 objects, otherwise - singleton
					foreach($maybeSplitedRanges as $oneRange){
					
						$indexedArr[$titleToIndex['day']] = $oneRange['day'];
						$indexedArr[$titleToIndex['start']] = $oneRange['start'];
						$indexedArr[$titleToIndex['end']] = $oneRange['end'];
						
						$attemptedToEnter++;
						if($loadToDB){
							// one entry for every range
							$isOk = addEntryToHoursTable($conn,$indexedArr,$titleToIndex);
							if($isOk)
								$enteredNum++;
						}else{
							// one row for every range
							fwrite($write,implode(',',array_values($indexedArr)).$space);
						}
					}
				}
			}	
		}
	}
	
	// we want at least 75% of the venues downloaded to be in our DB
	return ($enteredNum/$attemptedToEnter>=0.75); // will be used in case that $loadToDB=1 to see if the city was loaded well
	
}

?>
