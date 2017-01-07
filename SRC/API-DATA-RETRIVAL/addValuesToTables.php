<?php

$serverName = "mysqlsrv.cs.tau.ac.il";
$userName = "DbMysql07";
$password = "DbMysql07";
$dbName = "DbMysql07";

//create connection
$conn = new mysqli($serverName,$userName,$password,$dbName);
//check connection
if ($conn->connect_error){
    die("connection failed ".$conn->connect_error);
}

//fillCategoryTable($conn);

$conn->close();


function fillCategoryTable($conn)
{
    $sql = $conn->prepare("INSERT INTO Category (id,name,suggested_countries) VALUES (?,?,?)");
    $sql->bind_param("sss", $id, $name, $suggested);

    $file = new SplFileObject("food_categories.txt");

    // Loop until we reach the end of the file.
    while (!$file->eof()) {
        // handle each category.
        $name = $file->fgets();
        $id = $file->fgets();
        $suggested = $file->fgets();
        if (substr($suggested, 0, 9) === "Suggested") {
            $suggested = trim($suggested, "Suggested Countries: ");
            $file->fgets();
        }

        if ($sql->execute() === TRUE) {
            echo "Added category " . $name . " successfully\n";
        } else {
            echo "Error creating category " . $name . ": " . $sql->error;
        }
    }

    // Unset the file to call __destruct(), closing the file handle.
    $file = null;
    $sql->close();
}

function addEntryToCityTable($conn,$VenueArr,$titleToIndex){
    $id=""; //get from external array
    $name=$VenueArr[$titleToIndex['city']];
    $state=$VenueArr[$titleToIndex['state']];
    $country=$VenueArr[$titleToIndex['country']];
    $lat=$VenueArr[$titleToIndex['lat']];
    $lon=-$VenueArr[$titleToIndex['lon']];

    $sql = $conn->prepare("INSERT INTO City (id,name,country,state,lat,lon) VALUES (?,?,?,?,?,?)");
    $sql->bind_param("isssdd",$id,$name,$state,$country,$lat,$lon);

    if ($sql->execute() === TRUE) {
        echo "Added city ".$name." successfully";
    } else {
        echo "ERROR while adding city ".$name. $sql->error;
    }

    $sql->close();
}

function addEntryToRestaurantTable($conn,$VenueArr,$titleToIndex)
{
    $id = $VenueArr[$titleToIndex['id']];
    $name = $VenueArr[$titleToIndex['name']];
    $cityId =""; //get from external file
    $url=$VenueArr[$titleToIndex['url']];
    $phone=$VenueArr[$titleToIndex['phone']];
    //has menu????
    $address=$VenueArr[$titleToIndex['address']];
    $categories=$VenueArr[$titleToIndex['categories']];
    $checkinsCount=$VenueArr[$titleToIndex['checkinsCount']];
    $usersCount=$VenueArr[$titleToIndex['usersCount']];
    $tipCount=$VenueArr[$titleToIndex['tipCount']];

    $sql = $conn->prepare("INSERT INTO Restaurant (id,name,city_id,url,phone,address,categories,checkinsCount,usersCount,tipCount) VALUES (?,?,?,?,?,?,?,?,?)");
    $sql->bind_param("ssissssiii",$id,$name,$cityId,$url,$phone,$address,$categories,$checkinsCount,$usersCount,$tipCount);

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

?>



/*

/* ADD CITY TO TABLE */


$sql = $conn->prepare("INSERT INTO City (id,name,country,state,lat,lon) VALUES (?,?,?,?,?,?)");
$sql->bind_param("isssdd",$id,$name,$state,$country,$lat,$lon);




$id=5;
$name="Berlin";
$state="Berlin";
$country="Germany";
$lat=52.48202889;
$lon=13.42482271;


if ($sql->execute() === TRUE) {
echo "Added city successfully";
} else {
echo "Error creating table: " . $sql->error;
}
*/



