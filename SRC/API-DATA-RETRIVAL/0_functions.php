<?php
require_once("php-foursquare-master/src/FoursquareApi.php");

// output
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




function getAndSaveJSON($foursquare,$requestType,$params,$fileName,$outputDir){
	// Perform a request to a public resource
	$response = $foursquare->GetPublic($requestType,$params); // might be null (if null - check fails file - the fail url is there)
	
	if(empty($response)){
		// TODO:
		echo "TODO: foursquare->GetPublic returns null in getAndSaveJSON<br>";
	}else{
		file_put_contents($outputDir.$fileName,$response);
	}

	// check if rate_limit_exceeded
	if($foursquare->rate_limit_exceeded){
		echo "rate limit exceeded<br>";
		exit;
	}	
}



function requestCityFunc($foursquare,$cityName,$boundingBox,$requestType,$categoryId,$outputDir,$splitNum){
	$outputDirArr = array_flip(scanDir($outputDir));

	list($bbMat,$deltaNS,$deltaEW) = getBoundingBoxMat($boundingBox,$splitNum);
	$c=0;
	foreach($bbMat as $lat=>$latArr){
		$size = sizeof($bbMat)*sizeof($bbMat[$lat]);
		//echo "number of requests for $cityName = $size<br>";
		//echo "time: ".($size/1700)." hours<br>";
		//return 0;
		//exit;
		
		foreach($latArr as $lon=>$stam){
			
			// TODO: check this
			$ne = formatCoodrdinates($lat).','.formatCoodrdinates($lon);
			$sw = formatCoodrdinates($lat-$deltaNS).','.formatCoodrdinates($lon-$deltaEW);
			
			
			// Prepare parameters
			$params = array("sw"=>"$sw",
							"ne"=>"$ne",
							"categoryId"=>$categoryId, // food category
							"intent"=>"browse");
			
			$nameParams = $params;
			array_unshift($nameParams,$cityName);
			
			// request api only if not exists
			$fileName = createFileNameByParams($nameParams);
			if(!array_key_exists($fileName,$outputDirArr)){
				getAndSaveJSON($foursquare,$requestType,$params,$fileName,$outputDir);
				$c++;
			}
		}
	}
}

function createFileNameByParams($params){
	$fileName = '';
	foreach($params as $key=>$val){
		$fileName = $fileName.str_replace(' ','_',$val).',';
	}
	return substr($fileName,0,strlen($fileName)-1).".json";
}

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


function getFieldOrNull($arr,$key,$isStr,$loadToDB){
	if(!array_key_exists($key,$arr))
		return null;
		
	// found:
	$val = $arr[$key];
	if($isStr){
		$val = fixString($val);
		if(!$loadToDB)
			$val = '"'.$val.'"';
	}
	return $val;
}

function venueJson2indexedArr($venueDetails,$titleToIndex,$loadToDB){
	$arrToWrite = array_fill(0,sizeof($titleToIndex),'');
	
	// main
	$arrToWrite[$titleToIndex['id']] = $venueDetails['id'];
	$arrToWrite[$titleToIndex['name']] = ($loadToDB ? $venueDetails['name'] : '"'.$venueDetails['name'].'"');
	$arrToWrite[$titleToIndex['url']] = getFieldOrNull($venueDetails,'url',1,$loadToDB);
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
		$arrToWrite[$titleToIndex['lat']] = 	getFieldOrNull($venueDetails['location'],'lat',1,$loadToDB);
		$arrToWrite[$titleToIndex['lon']] = 	getFieldOrNull($venueDetails['location'],'lng',1,$loadToDB);
	}
	// categories
	$categoriesArr = array();// in case we'll see it has more than one category
	foreach($venueDetails['categories'] as $j=>$categoryIdArr){
		$categoryId = $categoryIdArr['id'];
		//$categoryName = $categoryIdArr['name'];
		$categoriesArr[] = $categoryId;
	}
	$arrToWrite[$titleToIndex['categories']] = implode('+',$categoriesArr);
	// stats
	if(array_key_exists('stats',$venueDetails)){
		$arrToWrite[$titleToIndex['checkinsCount']] = getFieldOrNull($venueDetails['stats'],'checkinsCount',0,$loadToDB);
		$arrToWrite[$titleToIndex['usersCount']] 	= getFieldOrNull($venueDetails['stats'],'usersCount',0,$loadToDB);
		$arrToWrite[$titleToIndex['tipCount']] 		= getFieldOrNull($venueDetails['stats'],'tipCount',0,$loadToDB);
	}
	
	return $arrToWrite;
}

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

//note: city id is determined by line number in input file.
function getCity2idArr($fileName){
	$city2id = array();
	$read = fopen($fileName,'r') or die ("can't open file");
	$cityId = 0; //row number
	while(!feof($read)){
		$cityName = trim(fgets($read));
		if($cityName==='')
			continue;
		$city2id[$cityName] = $cityId++;
	}
	fclose($read);
	return $city2id;
}

function fstRow2IndexArr($line,$delimiter = ','){
	$arr = array();
	$parts = explode($delimiter,$line);
	foreach($parts as $i=>$title){		
		$arr[str_replace('"','',$title)] = $i;
	}
	return $arr;
}

function formatTime($timeStr){
	$isNextDay = false;
	if(strpos($timeStr,'+')!==false){
		$isNextDay = true;
		$timeStr = str_replace('+','',$timeStr);
	}
	list($hh,$mm) = str_split($timeStr,2);
	return array($hh.':'.$mm,$isNextDay);
}

function splitRangeIfNeeded($startFrame,$endFrame,$isNextDay,$day){
	$maybeSplitedRanges = array();
	if(!$isNextDay){
		$maybeSplitedRanges[] = array('start'=>$startFrame,'end'=>$endFrame,'day'=>$day);
	}else{
		$maybeSplitedRanges[] = array('start'=>$startFrame,'end'=>'00:00','day'=>$day);
		$maybeSplitedRanges[] = array('start'=>'00:00','end'=>$endFrame,'day'=>($day+1)%7);
	}
	return $maybeSplitedRanges;
}	
	
?>
