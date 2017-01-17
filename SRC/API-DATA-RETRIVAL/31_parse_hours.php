<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");
require_once("addValuesToTables.php");


$loadToDB = 0;
$titleToIndex = array('venueId'=>0,'day'=>1,'start'=>2,'end'=>3);


if($loadToDB){
	$conn = createConnection();
}else{	
	$space = "\r\n";
	$writeFileName = $csvDir.$hoursDir."hours.csv";
	$write = fopen($writeFileName,'w');
	fwrite($write,implode(',',array_keys($titleToIndex)).$space);
}

foreach(scandir($jsonsDir.$hoursDir) as $cityName){
	if($cityName==='.' || $cityName==='..')
		continue;
	
	foreach(scandir($jsonsDir.$hoursDir.$cityName) as $fileName){
		if(strpos($fileName,'.json')===false)
			continue;
		
		
		$jsonStr = file_get_contents($jsonsDir.$hoursDir.$cityName.'/'.$fileName);
		$jsonArr = json_decode($jsonStr,true);
		// TODO: make sure that the json is valid
		
		$venueId = substr($fileName,0,strpos($fileName,'.'));
		
		// TODO: Inbal
		//checking that the venue exists in th DB
		//($conn,$venue)
		
		// skip empty menu
		if(!isset($jsonArr['response']['hours']['timeframes']))
			continue;
		
		
		$indexedArr = array_fill(0,sizeof($titleToIndex),'');
		$indexedArr[$titleToIndex['venueId']] = $venueId;

		
		foreach($jsonArr['response']['hours']['timeframes'] as $i=>$oneTimeframe){ // convert json to indexed array and to line in csv / entry in DB
			foreach($oneTimeframe['days'] as $j=>$day){ 
				
				foreach($oneTimeframe['open'] as $k=>$rangeArr){
					list($startFrame,$notRelevant) = formatTime($rangeArr['start']);
					list($endFrame,$isNextDay) = formatTime($rangeArr['end']);
					
					$maybeSplitedRanges = splitRangeIfNeeded($startFrame,$endFrame,$isNextDay,$day); // if need to split - it will be array with 2 objects, otherwise - singleton
					foreach($maybeSplitedRanges as $oneRange){
					
						$indexedArr[$titleToIndex['day']] = $oneRange['day'];
						$indexedArr[$titleToIndex['start']] = $oneRange['start'];
						$indexedArr[$titleToIndex['end']] = $oneRange['end'];
		
						if($loadToDB){
							addEntryToHoursTable($conn,$indexedArr,$titleToIndex);
							//TODO: remove after test
							closeConnection($conn);
							exit;
							
						}else{
							// one row for every range
							fwrite($write,implode(',',array_values($indexedArr)).$space);
						}
					}
				}
			}	
		}
	}	
}

exit;



?>
