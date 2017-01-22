<?php

function createConnection(){
	$serverName = "mysqlsrv.cs.tau.ac.il";
	$userName = "DbMysql07";
	$password = "DbMysql07";
	$dbName = "DbMysql07";
	
	//create connection
	$conn = new mysqli($serverName,$userName,$password,$dbName);
	// Change character set to utf8
	mysqli_set_charset($conn,"utf8");
	
	//check connection
	if ($conn->connect_error){ 
		return false;
	}
	
	return $conn;
}


function closeConnection($conn){
	$conn->close();
}

// run only in backend. fills the Category table
function addEntryToCategoryTable($conn,$id,$name)
{
    $sql = $conn->prepare("INSERT INTO Category (id,name) VALUES (?,?)");
    $sql->bind_param("ss", $id, $name);
	
	if ($sql->execute() === TRUE) {
		echo "Added category " . $name . " successfully\n";
	} else {
		echo "Error creating category " . $name . ": " . $sql->error;
	}
    
    $sql->close();
}

// run only in backend. fills the CategoryMain table
function addEntryToCategoryMainTable($conn,$sonId,$mainId){
    $sql = $conn->prepare("INSERT INTO CategoryMain (category_id,main_id) VALUES (?,?)");
	$sql->bind_param("ss",$sonId,$mainId);

    if ($sql->execute() === TRUE) {
        echo "Added sonId ".$sonId." successfully";
    } else {
        echo "ERROR while adding son ".$sonId." ". $sql->error;
    }

    $sql->close();	
}

// add entry to City table, by $titleToIndex format in parsing script
function addEntryToCityTable($conn,$cityArr,$titleToIndex){
	$res = true;

	$name		= $cityArr[$titleToIndex['cityName']];
	$north_lat 	= $cityArr[$titleToIndex['north_lat']];
	$south_lat 	= $cityArr[$titleToIndex['south_lat']];
	$east_lon 	= $cityArr[$titleToIndex['east_lon']];
	$west_lon 	= $cityArr[$titleToIndex['west_lon']];
	
    $sql = $conn->prepare("INSERT INTO City (name,north_lat,south_lat,east_lon,west_lon) VALUES (?,?,?,?,?)");
    $sql->bind_param("sdddd",$name,$north_lat,$south_lat,$east_lon,$west_lon);
	
	// execute and error handling
    if ($sql->execute() !== TRUE) {
        if(strpos($sql->error,"constraint") === false) // error but NOT constraint (constant is a good error)
			$res = false;
    }

    $sql->close();
	return $res;
}

// add entry to Restaurant table, by $titleToIndex format in parsing script
function addEntryToRestaurantTable($conn,$venueArr,$titleToIndex){
	$res = true;
	
	$cityId 		= $venueArr[$titleToIndex['cityId']];
    $id 			= $venueArr[$titleToIndex['id']];
    $name 			= $venueArr[$titleToIndex['name']];
    $url			= $venueArr[$titleToIndex['url']];
    $hasMenu		= $venueArr[$titleToIndex['hasMenu']];
    $phone			= $venueArr[$titleToIndex['phone']];
    $address		= $venueArr[$titleToIndex['address']];
    $category		= $venueArr[$titleToIndex['category']];
    $checkinsCount	= $venueArr[$titleToIndex['checkinsCount']];
    $usersCount		= $venueArr[$titleToIndex['usersCount']];
    $tipCount		= $venueArr[$titleToIndex['tipCount']];
	
	
    $sql = $conn->prepare("INSERT INTO Restaurant (id,name,city_id,url,has_menu,phone,address,category_id,checkinsCount,usersCount,tipCount) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
	$sql->bind_param("ssisisssiii",$id,$name,$cityId,$url,$hasMenu,$phone,$address,$category,$checkinsCount,$usersCount,$tipCount);
	
	// execute and error handling
    if ($sql->execute() !== TRUE) {
        if(strpos($sql->error,"constraint") === false){ // error but NOT constraint (constant is a good error)
			$res = false;
		}
    }

    $sql->close();
	return $res;
}

// add entry to Dish table, by $titleToIndex format in parsing script
function addEntryToDishTable($conn,$DishArr,$titleToIndex){
	$res = true;
	
    $id 			= $DishArr[$titleToIndex['dishId']];
    $restaurantId 	= $DishArr[$titleToIndex['venueId']];
    $section 		= $DishArr[$titleToIndex['sectionName']];
    $name			= $DishArr[$titleToIndex['dishName']];
    $description	= $DishArr[$titleToIndex['description']];
    $price			= $DishArr[$titleToIndex['price']];

    $sql = $conn->prepare("INSERT INTO Dish (restaurant_id,section_name,name,description,price) VALUES (?,?,?,?,?)");
    $sql->bind_param("sssss",$restaurantId,$section,$name,$description,$price);
	
	// execute and error handling
    if ($sql->execute() !== TRUE) {
        if(strpos($sql->error,"constraint") === false) // error but NOT constraint (constant is a good error)
			$res = false;
    }

    $sql->close();
	return $res;
}

// add entry to OpenHours table, by $titleToIndex format in parsing script
function addEntryToHoursTable($conn,$indexedArr,$titleToIndex){
	$res = true;

	$venueId= $indexedArr[$titleToIndex['venueId']];
	$day	= $indexedArr[$titleToIndex['day']];
	$start	= str_replace(':','',$indexedArr[$titleToIndex['start']]);
	$end 	= str_replace(':','',$indexedArr[$titleToIndex['end']]);
	
    $sql = $conn->prepare("INSERT INTO OpenHours (restaurant_id,day,open_hour,close_hour) VALUES (?,?,?,?)");
	$sql->bind_param("siii",$venueId,$day,$start,$end);
	
	// execute and error handling
    if ($sql->execute() !== TRUE) {
        if(strpos($sql->error,"constraint") === false) // error but NOT constraint (constant is a good error)
			$res = false;
    }

    $sql->close();
	return $res;
}


// --- select from DB functions ---

// when inserting a new city, we want to make sure it's not in our DB already. boundingBox is it's unique identifier (not name. for example: "New York" vs. "NY")
function cityAlreadyInTableBB($conn,$boundingBox)
{
    $result = $conn->query("SELECT * FROM City where north_lat=".$boundingBox['north_lat']." and south_lat=".$boundingBox['south_lat']." and east_lon=".$boundingBox['east_lon']." and west_lon=".$boundingBox['west_lon']);

    if ($result->num_rows > 0) {
        return TRUE; // exists
    } else {
        return FALSE; // not
    }
}

// get city id by its name
function getCityIdByName($conn,$cityName){
	$result = $conn->query("SELECT id FROM City where name='$cityName'");
	
	if ($result->num_rows == 1) {
		$row = $result->fetch_assoc();
        return $row['id'];
    } else {
        return FALSE;
    }
}


// returns true if venue already in table and false otherwise
function venueAlreadyInTable($conn,$venueId)
{
	$result = $conn->query("SELECT * FROM Restaurant where id = '".$venueId."' LIMIT 1");

    if ($result->num_rows > 0) {
        return TRUE;
    } else {
        return FALSE;
    }
}

// returns ar indexed array of venue ids (restaurant ids) by city id that has menu (according to their venue json)
function getVenuesWithMenusArrFromDB($conn,$cityId){
	$result = $conn->query("SELECT id FROM Restaurant where city_id=$cityId and has_menu=1"); // this will work onlt after making new DB
	$arr = array();
	while ($row = $result->fetch_assoc()) {
		$arr[] = $row['id'];
	}
	
	return $arr;
}

// returns ar indexed array of venue ids (restaurant ids) by city id
function getVenuesArrFromDB($conn,$cityId){
	$result = $conn->query("SELECT id FROM Restaurant where city_id=$cityId");
	$arr = array();
	while ($row = $result->fetch_assoc()) {
		$arr[] = $row['id'];
	}
	
	return $arr;
}

// --- indexing ---

// Creating index (dish table)
function indexDish($conn){
	$result = $conn->query("CREATE FULLTEXT INDEX idx_Dish_name ON Dish (name);");
	return $result;
}

// Optimizing table Dish
function optimizeDishTable($conn){
	$result = $conn->query("OPTIMIZE TABLE Dish;");
	return $result;
}

function indexCityName($conn){
	echo "Creating index (city name).";
	$result = $conn->query("CREATE INDEX idx_City_name ON City(name);");
	return $result;
}

?>