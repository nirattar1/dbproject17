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

//createCityTable($conn);
//createCategoryTable($conn);
//createRestaurantTable($conn);
//createDishTable($conn);

$conn->close();


function createCityTable($conn)
{
    $sql = "CREATE TABLE City (
            id INT(6) PRIMARY KEY,
            name VARCHAR(30) NOT NULL,
            country VARCHAR(50) NOT NULL,
            state VARCHAR(50),
            lat DECIMAL(50) NOT NULL,
            lon DECIMAL(50) NOT NULL,
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
    $sql = "CREATE TABLE Category (
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
    $sql = "CREATE TABLE Restaurant (
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
    $sql = "CREATE TABLE Dish (
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

function createUserSelectionTable($conn)
{
	
    $sql = "CREATE TABLE UserSelection (
            category VARCHAR(32) NOT NULL PRIMARY KEY,
            badget VARCHAR(15),
            city VARCHAR(30),
            created_at TIMESTAMP
            )";

    if ($conn->query($sql) === TRUE) {
        echo "User Selection Table created successfully\n";
    } else {
        echo "Error creating table: " . $conn->error;
    }
}

?>
