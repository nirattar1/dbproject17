<?php
// $serverName = "mysqlsrv.cs.tau.ac.il";
// $userName = "DbMysql07";
// $password = "DbMysql07";
// $dbName = "DbMysql07";
$serverName = "localhost";
$userName = "root";
$password = "";
$dbName = "dbmysql07_local";

//create connection
$conn = new mysqli($serverName,$userName,$password,$dbName);
//check connection
if ($conn->connect_error){
    die("connection failed ".$conn->connect_error);
}

createCityTable($conn);
createCategoryTable($conn);
createRestaurantTable($conn);
createDishTable($conn);

$conn->close();


function createCityTable($conn)
{
    $sql = "CREATE TABLE IF NOT EXISTS City (
            id INT(6) PRIMARY KEY,
            name VARCHAR(30) NOT NULL,
			north_lat DECIMAL(50) NOT NULL,
			south_lat DECIMAL(50) NOT NULL,
			east_lon DECIMAL(50) NOT NULL,
			west_lon DECIMAL(50) NOT NULL,
            created_at TIMESTAMP
            )";

    if ($conn->query($sql) === TRUE) {
        echo "City Table created successfully\n";
    } else {
        echo "Error creating table: " . $conn->error;
    }
}




function createCategoryTable($conn)
{
    $sql = "CREATE TABLE IF NOT EXISTS Category (
            id VARCHAR(32) PRIMARY KEY,
            name VARCHAR(32) NOT NULL,
            suggested_countries VARCHAR(50),
            created_at TIMESTAMP
            )";

    if ($conn->query($sql) === TRUE) {
        echo "Category Table created successfully\n";
    } else {
        echo "Error creating table: " . $conn->error;
    }
}


function createRestaurantTable($conn)
{
    $sql = "CREATE TABLE IF NOT EXISTS Restaurant (
            id VARCHAR(32) PRIMARY KEY,
            name VARCHAR(256) NOT NULL,
            city_id INT(6) NOT NULL,
            FOREIGN KEY(city_id) REFERENCES City(id),
            url VARCHAR(32),
            phone VARCHAR(32),
            address VARCHAR(50),
            categories VARCHAR(256),
            checkinsCount INT(24),
            usersCount INT(24),
            tipCount INT(24),
            created_at TIMESTAMP
            )";

    if ($conn->query($sql) === TRUE) {
        echo "Restaurant Table created successfully\n";
    } else {
        echo "Error creating table: " . $conn->error;
    }
}


function createDishTable($conn)
{
    $sql = "CREATE TABLE IF NOT EXISTS Dish (
            id VARCHAR(32) PRIMARY KEY,
            restaurant_id VARCHAR(32) NOT NULL,
            FOREIGN KEY(restaurant_id) REFERENCES Restaurant(id),
            section_name VARCHAR(10),
            name VARCHAR(256),
            description VARCHAR(256),
            price VARCHAR(256),
            created_at TIMESTAMP
            )";

    if ($conn->query($sql) === TRUE) {
        echo "Dish Table created successfully\n";
    } else {
        echo "Error creating table: " . $conn->error;
    }
}

?>