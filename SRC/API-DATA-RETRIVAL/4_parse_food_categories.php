<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");
require_once("addValuesToTables.php");


$inputDir = 'input/';
$citiesInputFile = $inputDir."citiesInput.txt";
$loadToDB = 0; // otherwise - goes to csv
$writeVenuesWithMenuMode = 1;

// parse

$titleToIndex = array('main_category'=>0,'sun_catgory'=>1);

$city2idArr = getCity2idArr($citiesInputFile);

if($loadToDB){
	$conn = createConnection();
	//TODO
	//$conn->query("DELETE FROM Restaurant");
	//exit;
}else{	
/* 	$space = "\r\n";			
	$writeFileName = $csvDir.$venuesDir."14_01_17.csv";
	$write = fopen($writeFileName,'w');
	fwrite($write,implode(',',array_keys($titleToIndex)).$space);
	
	if($writeVenuesWithMenuMode)
		$writeVenuesWithMenu = fopen($inputDir."VenuesWithMenus.txt",'w');
}
*/
		$jsonStr = file_get_contents($inputDir.'categories_tree.json');

		$jsonArr = json_decode($jsonStr,true);

		foreach($jsonArr['response']['categories'][3]['categories'] as $i=>$categoryDepth1){ 
			$parentId = $categoryDepth1['id'];
			$sonsArr = getSonsCategoryIds($categoryDepth1['categories']);
			echo "<br>";
			echo $parentId."<br>";
			print_r($sonsArr);
		}
	
}
exit;
if($loadToDB){
	closeConnection($conn);
}else{
	fclose($write);
}



function getSonsCategoryIds($categoriesJson){
	$res = array();
	foreach($categoriesJson as $i=>$categoryDepth1){
		$res[] = $categoryDepth1['id'];
		// TODO: array_merge?
		array_merge($res,$categoryDepth1['categories']);
	}
	
	return $res;
}
?>
