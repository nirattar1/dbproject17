

<!DOCTYPE html>
<html>
	
<head>
<style>
p {
    font-family: "Comic Sans MS", cursive, sans-serif;
	text-align: center;
	font-size: 250%;
	
}
input {
	text-align: center 
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
				font-size: 150%;
}


</style>

</head>
<body>
<p>choose your favorite dish.. </p>

<?php 
function connect()
{
	$servername = "mysqlsrv.cs.tau.ac.il";
	$username = "DbMysql07";
	$password = "DbMysql07";
	$dbname = "DbMysql07";
	
	// $servername = "localhost";
	// $username = "root";
	// $password = "";
	// $dbname = "dbmysql07_local";
	
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
  	  die("Connection failed: " . $conn->connect_error);
	} 
	//echo "Connected successfully";
	
	return $conn;
}

//will get parameters.
//return SQL record set.
function runDishQuery ($conn, $cityId, $strDishSearchTerm,
	$items_per_page, $offset_items	)
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
			AND Dish.name LIKE '%$strDishSearchTerm%'
			ORDER BY price ASC
			LIMIT $items_per_page OFFSET $offset_items;";
	
	echo $sql ;
	
	//run query and return result
	$result = $conn->query($sql);
	return $result;
}


//"main"

//read input parameters.
if (!empty($_GET["city_id"]))
{
	$city_id = $_GET["city_id"];
}
else
{
	$city_id = 0; //default city
}

$dish_name_search = $_GET["dish_name_search"];
//default = ...

//make connection
$conn = connect(); 

//run main query
$items_per_page = 50;
$offset_items = 0;
$result = runDishQuery ($conn, $city_id, $dish_name_search, $items_per_page, $offset_items);
$numRows= $result->num_rows;
echo "returned rows: $numRows";

?>



</br>
</br>

<form action="dish.php?city_id=<?php echo $city_id?>" method="GET">

<input type="text" name="dish_name_search" id="dish_name_search">
<input type="submit" text="search">
</form>


<table>
<?php 

	//create table based on rows.
	while ($row = $result->fetch_assoc()) 
	{
		echo "<tr>";
		echo "	<td>" . $row['dish_name'] . "</td>";
		echo "	<td>" . $row['dish_price'] . "</td>";	
		echo "	<td>" . $row['rest_name'] . "</td>";
		echo "	<td>" . $row['dish_section'] . "</td>";	
		echo "	<td>" . $row['dish_description'] . "</td>";	
		echo "</tr>";		
	}
?>
</table>

</body>
</html>
