<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");

$loadToDB = 0;
$titleToIndex = array('venueId'=>0,'sectionName'=>1,'dishId'=>2,'dishName'=>3,'description'=>4,'price'=>5);


if($loadToDB){
	$conn = createConnection();
}else{	
	$space = "\r\n";
	$writeFileName = $csvDir.$menusDir."all.csv";
	$write = fopen($writeFileName,'w');
	fwrite($write,implode(',',array_keys($titleToIndex)).$space);
}

foreach(scandir($jsonsDir.$menusDir) as $fileName){
	if(strpos($fileName,'.json')===false)
		continue;
	
	$jsonStr = file_get_contents($jsonsDir.$menusDir.$fileName);
	$jsonArr = json_decode($jsonStr,true);
	// TODO: make sure that the json is valid
	
	$writeFileName = $csvDir.$menusDir.str_replace('.json','.csv',$fileName);
	$venueId = substr($fileName,0,strpos($fileName,'.'));
	
	// skip empty menu
	if($jsonArr['response']['menu']['menus']['count']===0)
		continue;
	
	$arrToWrite = array();
	foreach($jsonArr['response']['menu']['menus']['items'] as $i=>$oneMenu){ // convert menu json to indexed array and to line in csv
		// in case there is more than one menu for this venue
		// TODO: will we use it?
		$menuId = $oneMenu['menuId'];
		
		foreach($oneMenu['entries']['items'] as $j=>$itemSection){ // section in the menu: salads, sandwiches, desserts
			// menu section (like "Sandwiches")
			$sectionName = $itemSection['name']; // we must have this field, some times dish names can't be understood without the section name
			foreach($itemSection['entries']['items'] as $k=>$dishDetails){
				$indexedArr = dishJson2indexedArr($dishDetails,$titleToIndex,$loadToDB);
				
				$indexedArr[$titleToIndex['venueId']] = $venueId;
				$indexedArr[$titleToIndex['sectionName']] = '"'.$sectionName.'"';

				if($loadToDB){
					addEntryToRestaurantTable($conn,$indexedArr,$titleToIndex);
					//TODO: remove after test
					closeConnection($conn);
					exit;
					
				}else{
					// one row for every dish
					fwrite($write,implode(',',array_values($indexedArr)).$space);
				}
			}
			
		}
		
		
		// write
		// TODO: maybe we need to put it in 2 different table: venue to menuId, menuId to itemsInfo
		//fwrite($write,implode(',',array_values($indexedArr)).$space);
	}	
}

exit;



?>
