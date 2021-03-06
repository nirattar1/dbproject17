<?php
set_time_limit(0);
ini_set('memory_limit', '2024M');
require_once("addValuesToTables.php");

// all the functions in this file are run in backend to create tables in our DB

function createCityTable($conn)
{
    $sql = "CREATE TABLE IF NOT EXISTS City (
            id INT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(30) NOT NULL,
			north_lat DECIMAL(50,7) NOT NULL,
			south_lat DECIMAL(50,7) NOT NULL,
			east_lon DECIMAL(50,7) NOT NULL,
			west_lon DECIMAL(50,7) NOT NULL,
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
            url VARCHAR(256),
			has_menu INT(1),
            phone VARCHAR(32),
            address VARCHAR(50),
            category_id VARCHAR(32) NOT NULL,
            FOREIGN KEY(category_id) REFERENCES Category(id),
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
            id int(16) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            restaurant_id VARCHAR(32) NOT NULL,
            FOREIGN KEY(restaurant_id) REFERENCES Restaurant(id),
            section_name VARCHAR(256),
            name VARCHAR(256),
            description VARCHAR(256),
            price DECIMAL(8, 2),
            created_at TIMESTAMP
            )
			ENGINE=MyISAM;";

    if ($conn->query($sql) === TRUE) {
        echo "Dish Table created successfully\n";
    } else {
        echo "Error creating table: " . $conn->error;
    }
}

function createOpenHoursTable($conn)
{
    $sql = "CREATE TABLE IF NOT EXISTS OpenHours (
            restaurant_id VARCHAR(32),
            FOREIGN KEY(restaurant_id) REFERENCES Restaurant(id),
            day int(1),
            open_hour TIME,
            close_hour TIME,
            created_at TIMESTAMP
            )";

    if ($conn->query($sql) === TRUE) {
        echo "OpenHours Table created successfully\n";
    } else {
        echo "Error creating table: " . $conn->error;
    }
}

function createCategoryMainTable($conn)
{
    $sql = "CREATE TABLE IF NOT EXISTS CategoryMain (
            category_id VARCHAR(32) PRIMARY KEY,
			FOREIGN KEY(category_id) REFERENCES Category(id),
            main_id VARCHAR(32),
			FOREIGN KEY(main_id) REFERENCES Category(id),
            created_at TIMESTAMP
            )";

    if ($conn->query($sql) === TRUE) {
        echo "CategoryMain Table created successfully\n";
    } else {
        echo "Error creating table: " . $conn->error;
    }
}

?>