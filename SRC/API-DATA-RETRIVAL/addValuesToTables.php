<?php

function createConnection(){
	$serverName = "mysqlsrv.cs.tau.ac.il";
	$userName = "DbMysql07";
	$password = "DbMysql07";
	$dbName = "DbMysql07";
	
/* 	$serverName = "localhost";
	$userName = "root";
	$password = "";
	$dbName = "dbmysql07_local"; */

	//create connection
	$conn = new mysqli($serverName,$userName,$password,$dbName);
	// Change character set to utf8
	mysqli_set_charset($conn,"utf8");
	
	//check connection
	if ($conn->connect_error){
		die("connection failed ".$conn->connect_error);
	}
	
	return $conn;
}


function closeConnection($conn){
	$conn->close();
}

//fillCategoryTable($conn);
function addEntryToCategoryTable($conn,$id,$name)
{
    $sql = $conn->prepare("INSERT INTO Category (id,name) VALUES (?,?)");
    $sql->bind_param("ss", $id, $name);
	
	if ($sql->execute() === TRUE) {
		echo "Added category " . $name . " successfully\n";
	} else {
		echo "Error creating category " . $name . ": " . $sql->error;
	}
    

    // Unset the file to call __destruct(), closing the file handle.
    $file = null;
    $sql->close();
}

function addEntryToCityTable($conn,$cityArr,$titleToIndex){
	$sql = $conn->query("select * City");
	
    $id = $result->num_rows;
    //$id=$cityArr[$titleToIndex['cityId']];
    $name=$cityArr[$titleToIndex['cityName']];
	$north_lat = $cityArr[$titleToIndex['north_lat']];
	$south_lat = $cityArr[$titleToIndex['south_lat']];
	$east_lon = $cityArr[$titleToIndex['east_lon']];
	$west_lon = $cityArr[$titleToIndex['west_lon']];
	
    $sql = $conn->prepare("INSERT INTO City (id,name,north_lat,south_lat,east_lon,west_lon) VALUES (?,?,?,?,?,?)");
    $sql->bind_param("isdddd",$id,$name,$north_lat,$south_lat,$east_lon,$west_lon);

    if ($sql->execute() === TRUE) {
        echo "Added city ".$name." successfully";
    } else {
        echo "ERROR while adding city ".$name. $sql->error;
    }

    $sql->close();
}

//$titleToIndex = array('cityId'=>0,'id'=>1,'name'=>2,'url'=>3,'hasMenu'=>4,'phone'=>5,
//				'address'=>6,'city'=>7,'state'=>8,'country'=>9,'lat'=>10,'lon'=>11,
//				'category'=>12,'checkinsCount'=>13,'usersCount'=>14,'tipCount'=>15);

function addEntryToRestaurantTable($conn,$VenueArr,$titleToIndex)
{
	$cityId = $VenueArr[$titleToIndex['cityId']];
    $id = $VenueArr[$titleToIndex['id']];
    $name = $VenueArr[$titleToIndex['name']];
    $url=$VenueArr[$titleToIndex['url']];
    $hasMenu=$VenueArr[$titleToIndex['hasMenu']];
    $phone=$VenueArr[$titleToIndex['phone']];
    $address=$VenueArr[$titleToIndex['address']];
    $category=$VenueArr[$titleToIndex['categories']];
    $checkinsCount=$VenueArr[$titleToIndex['checkinsCount']];
    $usersCount=$VenueArr[$titleToIndex['usersCount']];
    $tipCount=$VenueArr[$titleToIndex['tipCount']];

    $sql = $conn->prepare("INSERT INTO Restaurant (id,name,city_id,url,has_menu,phone,address,category,checkinsCount,usersCount,tipCount) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $sql->bind_param("ssisisssiii",$id,$name,$cityId,$url,$hasMenu,$phone,$address,$category,$checkinsCount,$usersCount,$tipCount);
	
    if ($sql->execute() === TRUE) {
        echo "Added restaurant ".$name." successfully";
    } else {
        echo "ERROR while adding restaurant ".$name. $sql->error;
    }

    $sql->close();
}

function addEntryToDishTable($conn,$DishArr,$titleToIndex)
{
    $id = $DishArr[$titleToIndex['dishId']];
    $restaurantId = $DishArr[$titleToIndex['venueId']];
    $section =$DishArr[$titleToIndex['sectionName']];
    $name=$DishArr[$titleToIndex['dishName']];
    $description=$DishArr[$titleToIndex['description']];
    $price=$DishArr[$titleToIndex['price']];

    $sql = $conn->prepare("INSERT INTO Dish (id,restaurant_id,section_name,name,description,price) VALUES (?,?,?,?,?,?)");
    $sql->bind_param("ssssss",$id,$restaurantId,$section,$name,$description,$price);

    if ($sql->execute() === TRUE) {
        echo "Added dish ".$name." successfully";
    } else {
        echo "ERROR while adding dish ".$name. $sql->error;
    }

    $sql->close();
}

function addEntryToCategoryMainTable($conn,$sonId,$mainId){
    $sql = $conn->prepare("INSERT INTO CategoryMain (category_id,main_id) VALUES (?,?)");
	$sql->bind_param("ss",$sonId,$mainId);

    if ($sql->execute() === TRUE) {
        echo "Added sonId ".$sonId." successfully";
    } else {
        echo "ERROR while adding dish ".$sonId. $sql->error;
    }

    $sql->close();	
}

//returns true if city already in table and false otherwise
function cityAlreadyInTable($conn,$cityId)
{
    $result = $conn->query("SELECT * FROM City where id = $cityId LIMIT 1");

    if ($result->num_rows > 0) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function getCityIdByName($conn,$cityName){
	$result = $conn->query("SELECT id FROM City where name='$cityName'");
	if ($result->num_rows == 1) {
        return $result;
    } else {
        return FALSE;
    }
}


//returns true if venue already in table and false otherwise
function venueAlreadyInTable($conn,$venueId)
{
	$result = $conn->query("SELECT * FROM Restaurant where id = '".$venueId."' LIMIT 1");

    if ($result->num_rows > 0) {
        return TRUE;
    } else {
        return FALSE;
    }
}


function getVenuesWithMenusArrFromDB($conn,$cityId){
	$result = $conn->query("SELECT * FROM Restaurant where id =$cityId and ");
	
	
}


?>