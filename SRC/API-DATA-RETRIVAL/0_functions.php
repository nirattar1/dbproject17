<?php
require_once("php-foursquare-master/src/FoursquareApi.php");
ini_set('precision', 30);

// output
$jsonsDir = 'jsons/';
$csvDir = 'csv/';
if(!in_array(str_replace('/','',$jsonsDir),scandir('.')))
	mkdir($jsonsDir);
if(!in_array(str_replace('/','',$csvDir),scandir('.')))
	mkdir($csvDir);


$venuesDir = 'venues/';
$menusDir = 'menus/';
if(!in_array(str_replace('/','',$venuesDir),scandir($jsonsDir)))
	mkdir($jsonsDir.$venuesDir);
if(!in_array(str_replace('/','',$menusDir),scandir($jsonsDir)))
	mkdir($jsonsDir.$menusDir);
if(!in_array(str_replace('/','',$venuesDir),scandir($csvDir)))
	mkdir($csvDir.$venuesDir);
if(!in_array(str_replace('/','',$menusDir),scandir($csvDir)))
	mkdir($csvDir.$menusDir);




function getAndSaveJSON($foursquare,$requestType,$params,$fileName,$outputDir){
	
	// Perform a request to a public resource
	$response = $foursquare->GetPublic($requestType,$params);
	file_put_contents($outputDir.$fileName,$response);
}

function requestCityFuncTest($foursquare,$requestType,$outputDir,$delta){
	$params = array("sw"=>"33.7052060000000039963197195902,-118.245588999999995394318830222",
					"ne"=>"33.7955060000000031550371204503,-118.155289",
					"categoryId"=>"4d4b7105d754a06374d81259", // food category
					"intent"=>"browse");
	
	$fileName = "test.json";
	getAndSaveJSON($foursquare,$requestType,$params,$fileName,$outputDir);
}

function requestCityFunc($foursquare,$cityName,$boundingBox,$requestType,$outputDir,$delta){
	$outputDirArr = array_flip(scanDir($outputDir));

	$bbMat = getBoundingBoxMat($boundingBox,$delta);
	
	foreach($bbMat as $lat=>$latArr){
		foreach($latArr as $lon=>$stam){
			
			// TODO: check this
			$ne = $lat.','.$lon;
			$sw = $lat-$delta.','.$lon-$delta;
			
			// Prepare parameters
			$params = array("sw"=>"$sw",
							"ne"=>"$ne",
							"categoryId"=>"4d4b7105d754a06374d81259", // food category
							"intent"=>"browse");
			
			$nameParams = $params;
			array_unshift($nameParams,$cityName);
			
			// request api only if not exists
			$fileName = createFileNameByParams($nameParams);
			if(!in_array($fileName,$outputDirArr))
				getAndSaveJSON($foursquare,$requestType,$params,$fileName,$outputDir);
		}
	}
}

function getBoundingBoxMat($boundingBox,$delta){
	$bbMat = array();
	
	$north = $boundingBox['north_lat'];
	$south = $boundingBox['south_lat'];
	$east  = $boundingBox['east_lon'];
	$west  = $boundingBox['west_lon'];
	
	// are we guaranteed:
	// $north>$south
	// $east>$west)
	for($lat=$north ;$lat>$south; $lat=$lat-$delta){
		$bbMat[(string)$lat] = array();
		for($lon=$east ;$lon>$west; $lon=$lon-$delta){
			$bbMat[(string)$lat][(string)$lon] = 0;
		}
	}
	
	return $bbMat;
}

function venueJson2indexedArr($venueDetails,$titleToIndex){
	$arrToWrite = array();
	
	// main
	$arrToWrite[$titleToIndex['id']] = $venueDetails['id'];
	$arrToWrite[$titleToIndex['name']] = '"'.$venueDetails['name'].'"';
	$arrToWrite[$titleToIndex['url']] = (array_key_exists('url',$venueDetails) ? $venueDetails['url'] : '');
	$arrToWrite[$titleToIndex['hasMenu']] = (array_key_exists('menu',$venueDetails) ? 1 : 0 );
	
	// contanct
	$arrToWrite[$titleToIndex['phone']] = (array_key_exists('formattedPhone',$venueDetails['contact']) ? $venueDetails['contact']['formattedPhone'] : '');
	// location
	$arrToWrite[$titleToIndex['address']] = '"'.$venueDetails['location']['address'].'"';
	$arrToWrite[$titleToIndex['city']] = '"'.$venueDetails['location']['city'].'"';
	$arrToWrite[$titleToIndex['state']] = '"'.$venueDetails['location']['state'].'"';
	$arrToWrite[$titleToIndex['country']] = '"'.$venueDetails['location']['country'].'"';
	$arrToWrite[$titleToIndex['lat']] = '"'.$venueDetails['location']['lat'].'"';
	$arrToWrite[$titleToIndex['lon']] = '"'.$venueDetails['location']['lng'].'"';
	// categories
	$categoriesArr = array();// in case we'll see it has more than one category
	foreach($venueDetails['categories'] as $j=>$categoryIdArr){
		$categoryId = $categoryIdArr['id'];
		//$categoryName = $categoryIdArr['name'];
		$categoriesArr[] = $categoryId;
	}
	$arrToWrite[$titleToIndex['categories']] = implode('+',$categoriesArr);
	// stats
	$arrToWrite[$titleToIndex['checkinsCount']] = $venueDetails['stats']['checkinsCount'];
	$arrToWrite[$titleToIndex['usersCount']] = $venueDetails['stats']['usersCount'];
	$arrToWrite[$titleToIndex['tipCount']] = $venueDetails['stats']['tipCount'];
	$arrToWrite[$titleToIndex['beenHere']] = $venueDetails['beenHere']['lastCheckinExpiredAt'];
	
	return $arrToWrite;
}

function dishJson2indexedArr($dishDetails,$titleToIndex){
	$arrToWrite = array_fill(0,sizeof($titleToIndex),'');
	
	$arrToWrite[$titleToIndex['dishId']] = $dishDetails['entryId'];
	$arrToWrite[$titleToIndex['dishName']] = '"'.fixString($dishDetails['name']).'"';
	$arrToWrite[$titleToIndex['description']] = '"'.fixString(array_key_exists('description',$dishDetails) ? $dishDetails['description'] : "").'"';
	$arrToWrite[$titleToIndex['price']] = (array_key_exists('price',$dishDetails)? $dishDetails['price'] : null);

	return $arrToWrite;
}

function fixString($str){
	$str = str_replace(array('	  ','"'),'',$str);
	// TODO: not sure about it
	//$str = str_replace(',','',$str);
	return $str;
}

?>