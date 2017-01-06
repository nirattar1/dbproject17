<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("php-foursquare-master/src/FoursquareApi.php");
require_once("0_functions.php");



// Set your client key and secret
//$client_key = "3ZGILD2SYIGKM4NVBRIG4AWODIU4TUR02BEOCN21NDXIQNP1";
$client_key = "PNQBKVKJSRGNN4NXJGMU2J2X1OXWLGM2W5ARZDYDAPUXEJWN";
$client_secret = "CXUSCYJ14XAMKCNYQFLQ2LB45HCAORYHQKDENQQTGGEGJMTB";
// Load the Foursquare API library

if($client_key=="" or $client_secret=="")
{
	echo 'Load client key and client secret from <a href="https://developer.foursquare.com/">foursquare</a>';
	exit;
}

$foursquare = new FoursquareApi($client_key,$client_secret);
$location = array_key_exists("location",$_GET) ? $_GET['location'] : "Montreal, QC";


// --------------------------------------

if($requestMenus){
	$vanuesArr = array();

	foreach(scandir($jsonsDir.$venuesDir) as $fileName){
		if( $fileName==='.' ||  $fileName==='..')
			continue;
		
		$jsonStr = file_get_contents($jsonsDir.$venuesDir.$fileName);
		$jsonArr = json_decode($jsonStr,true);
		// TODO: make sure that the json i valid
		
		//print_r($jsonArr['response']['venues']);
		
		echo "fileName=$fileName<br>".sizeof($jsonArr['response']['venues'])."<br>";
		
		foreach($jsonArr['response']['venues'] as $i=>$venueDetails){
			//TODO: if has menu
			$id = $venueDetails['id'];
			$vanuesArr[$id] = 0;
		}
	}

	print_r($vanuesArr);

	foreach($vanuesArr as $id=>$s){
		$requestType = "venues/$id/menu";
		$params = array();
		$nameParams = array($id);
		
		// request api only if not exists
		$fileName = createFileNameByParams($nameParams);
		if(!in_array($fileName,$outputDirArr))
			getAndSaveJSON($foursquare,$requestType,$params,$nameParams,$jsonsDir.$menusDir);
	}
}

if($parseMenus){
	$space = "\r\n";
	$titleToIndex = array('venueId'=>0,'sectionName'=>1,'dishId'=>2,'dishName'=>3,'description'=>4,'price'=>5);
					
	$writeFileName = $csvDir.$menusDir."all.csv";
	$write = fopen($writeFileName,'w');
	fwrite($write,implode(',',array_keys($titleToIndex)).$space);
	
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
					$indexedArr = dishJson2indexedArr($dishDetails,$titleToIndex);
					
					$indexedArr[$titleToIndex['venueId']] = $venueId;
					$indexedArr[$titleToIndex['sectionName']] = '"'.$sectionName.'"';
					
					// one row for every dish
					fwrite($write,implode(',',array_values($indexedArr)).$space);
				}
				
			}
			
			
			// write
			// TODO: maybe we need to put it in 2 different table: venue to menuId, menuId to itemsInfo
			//fwrite($write,implode(',',array_values($indexedArr)).$space);
		}	
	}
	
}

exit;






$venues = json_decode($response);

foreach($venues->response->venues as $venue){
	if(isset($venue->categories['0']))
	{
		echo '<image class="icon" src="'.$venue->categories['0']->icon->prefix.'88.png"/>';
	}
	else
		echo '<image class="icon" src="https://foursquare.com/img/categories/building/default_88.png"/>';
	echo '<a href="https://foursquare.com/v/'.$venue->id.'" target="_blank"/><b>';
	echo $venue->name;
	echo "</b></a><br/>";
	
	
		
	if(isset($venue->categories['0']))
	{
		if(property_exists($venue->categories['0'],"name"))
		{
			echo ' <i> '.$venue->categories['0']->name.'</i><br/>';
		}
	}
	
	if(property_exists($venue->hereNow,"count"))
	{
			echo ''.$venue->hereNow->count ." people currently here <br/> ";
	}

	echo '<b><i>History</i></b> :'.$venue->stats->usersCount." visitors , ".$venue->stats->checkinsCount." visits ";
}

function createFileNameByParams($params){
	$fileName = '';
	foreach($params as $key=>$val){
		$fileName = $fileName.$val.',';
	}
	return substr($fileName,0,strlen($fileName)-1).".json";
}

function createCitiesLatLonArr($citiesLatLonFile){
	$citiesLatLonArr = array();
	
	$read = fopen($citiesLatLonFile,'r') or die ("can't open file $citiesLatLonFile");
	
	//$titles = trim(fgets($read));
	//$titlesArr = fstRow2IndexArr(trim(fgets($read)),';');
	
	fgets($read); // skipping title row: city_name,center_lat,center_lon,max_lat,max_lon,min_lat,min_lon
	while(!feof($read)){
		list($city_name,$center_lat,$center_lon,$max_lat,$max_lon,$min_lat,$min_lon) = fgetcsv($read);
		
		$citiesLatLonArr[$city_name] = 	array('center_lat' => $center_lat,'center_lon' => $center_lon,
												'max_lat' => $max_lat,'max_lon' => $max_lon,
												'min_lat' => $min_lat,'min_lon' => $min_lon);
	}
	
	return $citiesLatLonArr;
}

function fstRow2IndexArr($line,$delimiter = ','){
	$arr = array();
	$parts = explode($delimiter,$line);
	foreach($parts as $i=>$title){		
		$arr[str_replace('"','',$title)] = $i;
	}
	return $arr;
}


?>