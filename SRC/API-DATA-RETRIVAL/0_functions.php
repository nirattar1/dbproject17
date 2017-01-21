<?php
require_once("php-foursquare-master/src/FoursquareApi.php");

// output directories - mutual to all retrival-data scripts
// create directories if not exists
$inputDir = 'input/';
$jsonsDir = 'jsons/';
$csvDir = 'csv/';
if(!in_array(str_replace('/','',$jsonsDir),scandir('.')))
	mkdir($jsonsDir);
if(!in_array(str_replace('/','',$csvDir),scandir('.')))
	mkdir($csvDir);


$venuesDir = 'venues/';
$menusDir = 'menus/';
$hoursDir = 'hours/';
if(!in_array(str_replace('/','',$venuesDir),scandir($jsonsDir)))
	mkdir($jsonsDir.$venuesDir);
if(!in_array(str_replace('/','',$menusDir),scandir($jsonsDir)))
	mkdir($jsonsDir.$menusDir);
if(!in_array(str_replace('/','',$hoursDir),scandir($jsonsDir)))
	mkdir($jsonsDir.$hoursDir);
if(!in_array(str_replace('/','',$venuesDir),scandir($csvDir)))
	mkdir($csvDir.$venuesDir);
if(!in_array(str_replace('/','',$menusDir),scandir($csvDir)))
	mkdir($csvDir.$menusDir);


// --- mutual to all retrival-data scripts ---

// foursquare object to use the class FoursquareApi
// $i is the script number creating this object, for easier logs writing
function createNewFoursqaure($i){
	// Set your client key and secret
	$client_key = "PNQBKVKJSRGNN4NXJGMU2J2X1OXWLGM2W5ARZDYDAPUXEJWN";
	$client_secret = "CXUSCYJ14XAMKCNYQFLQ2LB45HCAORYHQKDENQQTGGEGJMTB";
	$requestsOutputFile = $i."_requests.txt";
	$failsOutputFile = $i."_failed_requests.txt";
	
	return new FoursquareApi($client_key,$client_secret,$requestsOutputFile,$failsOutputFile);
}

// perform request to foursquare api
// returns true if was successfull, otherwise - false
function getAndSaveJSON($foursquare,$requestType,$params,$fileName,$outputDir){
	// Perform a request
	$response = $foursquare->GetPublic($requestType,$params); // might be null (if null - check fails file - the fail url is there)
	
	if(empty($response)){
		return false;
	}else{
		file_put_contents($outputDir.$fileName,$response);
	}

	// check if rate_limit_exceeded
	if($foursquare->rate_limit_exceeded){
		// TODO
		echo "rate limit exceeded<br>";
		exit;
	}
	
	return true;
}

function createFileNameByParams($params){
	$fileName = '';
	foreach($params as $key=>$val){
		$fileName = $fileName.str_replace(' ','_',$val).',';
	}
	return substr($fileName,0,strlen($fileName)-1).".json";
}


// --- used in 1_search_venues.php ---

// check that the boundingBox is inside of USA's boundingBox
function inUSA($boundingBox){
	// based on USA boundingBox (from google api)
	$a = $boundingBox['north_lat'] < 71.5388001;
	$b = $boundingBox['south_lat'] > 18.7763;
	$c = $boundingBox['east_lon'] < -66.885417; // from -66.885417 to -180
	$d = $boundingBox['west_lon'] > 170.5957;	// from 170.5957 to 180
	
	return ($a && $b && ($c || $d));
}


// creates the requests by splitting the city to few rectangles to get more venues for the city
// returns the number of good requests done
function requestCityFunc($foursquare,$cityName,$boundingBox,$requestType,$categoryId,$outputDir,$splitNum){
	$requestsNum = 0;
	$outputDirArr = array_flip(scanDir($outputDir));

	// splitting the city to few rectangles
	list($bbMat,$deltaNS,$deltaEW) = getBoundingBoxMat($boundingBox,$splitNum);

	foreach($bbMat as $lat=>$latArr){	
		foreach($latArr as $lon=>$stam){
			$ne = formatCoodrdinates($lat).','.formatCoodrdinates($lon);
			$sw = formatCoodrdinates($lat-$deltaNS).','.formatCoodrdinates($lon-$deltaEW);
			
			// Prepare parameters
			$params = array("sw"=>"$sw",
							"ne"=>"$ne",
							"categoryId"=>$categoryId, // food category
							"intent"=>"browse");
			// build fileName
			$nameParams = $params;
			array_unshift($nameParams,$cityName);
			$fileName = createFileNameByParams($nameParams);
			
			// request api only if not exists
			if(!array_key_exists($fileName,$outputDirArr)){
				$isGoodResponse = getAndSaveJSON($foursquare,$requestType,$params,$fileName,$outputDir);
				if($isGoodResponse)
					$requestsNum++;
			}
		}
	}
	return $requestsNum;
}

// splitting the city to few rectangles
function getBoundingBoxMat($boundingBox,$splitNum){
	$bbMat = array();
	
	$north = $boundingBox['north_lat'];
	$south = $boundingBox['south_lat'];
	$east  = $boundingBox['east_lon'];
	$west  = $boundingBox['west_lon'];
	
	$deltaNS = ($north-$south)/$splitNum;
	$deltaEW = ($east-$west)/$splitNum;
	
	// are we guaranteed:
	// $north>$south
	// $east>$west)
	for($lat=formatCoodrdinates($north) ;$lat>$south; $lat=formatCoodrdinates($lat-$deltaNS)){
		$bbMat[(string)$lat] = array();
		for($lon=formatCoodrdinates($east) ;$lon>$west; $lon=formatCoodrdinates($lon-$deltaEW)){
			$bbMat[(string)$lat][(string)$lon] = 0;
		}
	}
	
	return array($bbMat,$deltaNS,$deltaEW);
}

function formatCoodrdinates($coord){
	list($head,$tail) = explode('.',$coord);
	return $head.'.'.substr($tail,0,4);
}

/// --- parsing foursquare jsons ---
function getFieldOrNull($arr,$key,$isStr,$loadToDB){
	// field doesn't exist:
	if(!array_key_exists($key,$arr))
		return null;
		
	// field exists:
	$val = $arr[$key];
	if($isStr){
		$val = fixString($val);
		if(!$loadToDB)
			$val = '"'.$val.'"';
	}
	return $val;
}

// parse venue (restaurant)
function venueJson2indexedArr($venueDetails,$titleToIndex,$loadToDB){
	$arrToWrite = array_fill(0,sizeof($titleToIndex),'');
	
	// main
	$arrToWrite[$titleToIndex['id']] = $venueDetails['id'];
	$arrToWrite[$titleToIndex['name']] = ($loadToDB ? $venueDetails['name'] : '"'.$venueDetails['name'].'"');
	$arrToWrite[$titleToIndex['url']] = getFieldOrNull($venueDetails,'url',0,$loadToDB);
	$arrToWrite[$titleToIndex['hasMenu']] = (array_key_exists('menu',$venueDetails) ? 1 : 0 );
	
	// contanct
	if(array_key_exists('contact',$venueDetails)){
		$arrToWrite[$titleToIndex['phone']] = getFieldOrNull($venueDetails['contact'],'formattedPhone',1,$loadToDB);
	}
	// location
	if(array_key_exists('location',$venueDetails)){
		$arrToWrite[$titleToIndex['address']] = getFieldOrNull($venueDetails['location'],'address',1,$loadToDB);
		$arrToWrite[$titleToIndex['city']] = 	getFieldOrNull($venueDetails['location'],'city',1,$loadToDB);
		$arrToWrite[$titleToIndex['state']] = 	getFieldOrNull($venueDetails['location'],'state',1,$loadToDB);
		$arrToWrite[$titleToIndex['country']] = getFieldOrNull($venueDetails['location'],'country',1,$loadToDB);
	}
	// category
	$arrToWrite[$titleToIndex['category']] = $venueDetails['categories'][0]['id'];// we always have exactly one (checked)

	// stats
	if(array_key_exists('stats',$venueDetails)){
		$arrToWrite[$titleToIndex['checkinsCount']] = getFieldOrNull($venueDetails['stats'],'checkinsCount',0,$loadToDB);
		$arrToWrite[$titleToIndex['usersCount']] 	= getFieldOrNull($venueDetails['stats'],'usersCount',0,$loadToDB);
		$arrToWrite[$titleToIndex['tipCount']] 		= getFieldOrNull($venueDetails['stats'],'tipCount',0,$loadToDB);
	}
	
	return $arrToWrite;
}

// parse dish
function dishJson2indexedArr($dishDetails,$titleToIndex,$loadToDB){
	$arrToWrite = array_fill(0,sizeof($titleToIndex),'');
	
	$arrToWrite[$titleToIndex['dishId']] = 		getFieldOrNull($dishDetails,'entryId',0,$loadToDB);
	$arrToWrite[$titleToIndex['dishName']] = 	getFieldOrNull($dishDetails,'name',1,$loadToDB);
	$arrToWrite[$titleToIndex['description']] = getFieldOrNull($dishDetails,'description',1,$loadToDB);
	$arrToWrite[$titleToIndex['price']] = 		getFieldOrNull($dishDetails,'price',0,$loadToDB);

	return $arrToWrite;
}

function fixString($str){
	$str = str_replace(array('	  ','"','*','^'),'',$str);
	$str = preg_replace('/[^A-Za-z0-9\- ]/', '', $str);
	return $str;
}

// --- 31_parse_hours functions ---
// deal with time range that ends the next day (hour has +) - and saving a param that says that we need to split that range
function formatTime($timeStr){
	$isNextDay = false;
	if(strpos($timeStr,'+')!==false){
		if($timeStr==='+0000'){ //still the same day
			$timeStr = '2359';
		}else{
			$isNextDay = true;
			$timeStr = str_replace('+','',$timeStr);
		}
	}
	list($hh,$mm) = str_split($timeStr,2);
	return array($hh.':'.$mm.':00',$isNextDay);
}

// split time range that ends the next day
function splitRangeIfNeeded($startFrame,$endFrame,$isNextDay,$day){
	$maybeSplitedRanges = array();
	if(!$isNextDay){
		$maybeSplitedRanges[] = array('start'=>$startFrame,'end'=>$endFrame,'day'=>$day);
	}else{
		$nextDay = ( ($day+1)%7 ===0 ? 7 : ($day+1)%7);
		$maybeSplitedRanges[] = array('start'=>$startFrame,'end'=>'23:59:59','day'=>$day);
		$maybeSplitedRanges[] = array('start'=>'00:00:00','end'=>$endFrame,'day'=>$nextDay);
	}
	return $maybeSplitedRanges;
}	
	
?>
