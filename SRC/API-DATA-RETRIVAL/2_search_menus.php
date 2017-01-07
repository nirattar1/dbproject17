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

$foursquare = new FoursquareApi($client_key,$client_secret,$requestsOutputFile,$failsOutputFile);
$location = array_key_exists("location",$_GET) ? $_GET['location'] : "Montreal, QC";


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


?>
