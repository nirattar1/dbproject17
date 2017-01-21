<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("0_functions.php");
require_once("addValuesToTables.php");


$loadToDB = 1; // otherwise - goes to csv

// parse

$titleToIndex = array('main_category'=>0,'son_catgory'=>1);


if($loadToDB){
	$conn = createConnection();
}else{
	// for testing before loading to the DB
 	$space = "\r\n";			
	$writeFileName = $csvDir."categories.csv";
	$write = fopen($writeFileName,'w');
	fwrite($write,implode(',',array_keys($titleToIndex)).$space);
}

$jsonStr = file_get_contents($inputDir.'categories_tree.json');

$jsonArr = json_decode($jsonStr,true);

foreach($jsonArr['response']['categories'][3]['categories'] as $i=>$categoryDepth1){ 
	$mainId = $categoryDepth1['id'];
	$mainName = $categoryDepth1['name'];
	if($loadToDB){
		addEntryToCategoryTable($conn,$mainId,$mainName);
		addEntryToCategoryMainTable($conn,$mainId,$mainId);
	}
	
	$sonsArr = getSonsCategoryIds($categoryDepth1['categories'],$conn,$loadToDB);
	foreach($sonsArr as $sonId){
		if($loadToDB)
			addEntryToCategoryMainTable($conn,$sonId,$mainId);
		else
			fwrite($write,$mainId.','.$sonId.$space);
	}
}
	


if($loadToDB){
	closeConnection($conn);
}else{
	fclose($write);
}


function getSonsCategoryIds($categoriesJson,$conn,$loadToDB){
	$res = array();
	foreach($categoriesJson as $i=>$categoryDepth1){
		$id = $categoryDepth1['id'];
		$name = $categoryDepth1['name'];
		
		$res[] = $id;
		if($loadToDB)
			addEntryToCategoryTable($conn,$id,$name);
		
		// recursively add sub categories
		$res = array_merge($res,getSonsCategoryIds($categoryDepth1['categories'],$conn,$loadToDB));
	}
	
	return $res;
}
?>
