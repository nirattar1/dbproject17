<!DOCTYPE html>
<html>

<head>
    <style>
        p {
            font-family: "Comic Sans MS", cursive, sans-serif;
            text-align: center;
            font-size: 250%;
        }

        h1 {
            font-family: "Comic Sans MS", cursive, sans-serif;
            text-align: center;
            display: inline-block;
            font-size: 100%;
            position: center;
        }

        h2 {
            font-family: "Comic Sans MS", cursive, sans-serif;
            text-align: center;
            font-size: 150%;
        }
        input {
            text-align: center
            width: 170px;
            padding: 10px;
            cursor: pointer;
            display: inline-block;
            position: center;
            box-shadow: 6px 6px 5px;
            #999;
            -webkit-box-shadow: 6px 6px 5px #999;
            -moz-box-shadow: 6px 6px 5px #999;
            font-weight: bold;
            background: #8fbc8f;
            color: #000;
            border-radius: 10px;
            border: 1px solid #999;
            font-family: "Comic Sans MS", cursive, sans-serif;
            font-size: 100%;
        }

        .container {
            margin-left: 30%;
            margin-right: 15%;
        }

        table {
            border-spacing: 0.5rem;
            margin-left: 30%;
            margin-right: 15%;
        }

        tr {
            font-family: "Comic Sans MS", cursive, sans-serif;
            font-size: 100%;
            padding: 0.5rem;
        }

        tr:nth-child(6n+0) {
            background: hsl(150, 50%, 50%);
        }

        tr:nth-child(6n+1) {
            background: hsl(160, 60%, 50%);
        }

        tr:nth-child(6n+2) {
            background: hsl(170, 70%, 50%);
        }

        tr:nth-child(6n+3) {
            background: hsl(180, 80%, 50%);
        }

        tr:nth-child(6n+4) {
            background: hsl(190, 90%, 50%);
        }

        tr:nth-child(6n+5) {
            background: hsl(200, 99%, 50%);
        }
    </style>

</head>
<body style="background-image:url(background.jpg)">
<p>choose your favorite dish...</p>
<h2>Search for specific dish name (for example pizza)</h2>

<?php
require_once("connectToDB.php");
$story = $_GET["story"];
//will get parameters.
//return SQL record set.
function runDishQuery($conn, $cityId, $strDishSearchTerm,
                      $items_per_page, $offset_items)
{

    $sql = "SELECT 
		Dish.id AS dish_id, Dish.name AS dish_name, Dish.price AS dish_price,
		Restaurant.id AS rest_id, Restaurant.name AS rest_name, 
		Dish.section_name AS dish_section, Dish.description AS dish_description, 
		City.id AS city_id, City.name AS city_name

		FROM City 
			INNER JOIN Restaurant 
			ON (City.id=Restaurant.city_id)
			INNER JOIN Dish 
			ON(Dish.restaurant_id=Restaurant.id)
			
		WHERE Restaurant.city_id=$cityId
		AND Dish.price IS NOT NULL
		AND MATCH (Dish.name) AGAINST('$strDishSearchTerm' IN BOOLEAN MODE)
		ORDER BY price ASC
		LIMIT $items_per_page OFFSET $offset_items;";

    //run query and return result
    $result = $conn->query($sql);
    return $result;
}

//get city name. returns city Id.
//will return -1 on failure.
function getCityIdByName($conn, $cityName)
{
    $sql = "SELECT id FROM City WHERE name='$cityName'";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        return $row['id'];
    } else {
        return -1;
    }
}


//read input parameters.
if (!empty($_GET["city"])) {
    //get input from querystring, normalize it.
    $city_name = $_GET["city"];
    $city_name = str_replace('_', ' ', $city_name);
} else {
    $city_name = "New York"; //default city
}

$dish_name_search = $_GET["dish_name_search"];
//default = ...

//make connection
$conn = connect();

//run main query
$items_per_page = 50;
$offset_items = 0;

//find city id by name.
$city_id = getCityIdByName($conn, $city_name);
if ($city_id != -1) {
    $result = runDishQuery($conn, $city_id, $dish_name_search, $items_per_page, $offset_items);
} else {
    echo "invalid city name";
}
$numRows = $result->num_rows;
//echo "returned rows: $numRows";

?>

</br>
</br>

<form action="dish.php" method="GET">
    <input type="hidden" name="city" value="<?php echo $city_name ?>">
    <div class="container">
        <h1>Filter name:</h1>
        <input type="text" name="dish_name_search" id="dish_name_search">
        <input type="submit" text="search">
    </div>
</form>

</br>
</br>
<?php
if ($result->num_rows > 0) {
    ?>
    <table>
        <tr>
            <td><b>Dish</b></td>
            <td><b>Price</b></td>
            <td><b>Restaurant</b></td>
            <td><b>Menu Section</b></td>
            <td><b>Description</b></td>
        </tr>
        <?php
        //create table based on rows.
        while ($row = $result->fetch_assoc()) //iterate on all restaurants returned
        {
            //prepare link to restaurant
            $rest_name = $row['rest_name'];
            $rest_id = $row['rest_id'];
            $link_rest_page = "rest.php?id=$rest_id";

            //output row
            echo "<tr>";
            echo "	<td>" . $row['dish_name'] . "</td>";
            echo "	<td>" . $row['dish_price'] . "</td>";
            echo "	<td>	<a href=\"" . $link_rest_page . "\">" . $rest_name . "</a>		</td>";
            echo "	<td>" . $row['dish_section'] . "</td>";
            echo "	<td>" . $row['dish_description'] . "</td>";
            echo "</tr>";
        }
        ?>
    </table>

<?php 
} 
elseif($result->num_rows==0 && $dish_name_search != ''){
		
		echo '<script language="javascript">';
		echo 'alert("dish name '.$dish_name_search.' does not exist")';
		echo '</script>'; 
	
}

?>


<h3 align="center">
    <input style="text-align: center
				width: 170px;
				padding: 30px;
				margin: auto;
				cursor: pointer;
				position: center;
				box-shadow: 6px 6px 5px; #999;
				-webkit-box-shadow: 6px 6px 5px #999;
				-moz-box-shadow: 6px 6px 5px #999;
				font-weight: bold;
				background: #8fbc8f;
				color: #000;
				border-radius:
				10px; border: 1px solid #999;
				font-size: 150%;"
           type="button"
           value="back"
           align="center"
           onclick="window.location.href='citys.php?story=<?php echo $story ?>'"/>
</h3>
<?php $conn->close(); ?>
</body>
</html>
