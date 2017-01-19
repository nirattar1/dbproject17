<!DOCTYPE html>
<html>

<style>
    p {
        font-family: "Comic Sans MS", cursive, sans-serif;
        text-align: center;
        font-size: 250%;
    }
    h1 {
        font-family: "Comic Sans MS", cursive, sans-serif;
        text-align: center;
        font-size: 150%;
    }
</style>

<body>
<p>Choose Category</p>
<h1>Press on the food category you want to find restaurant for </h1>

<?php
require_once("connectToDB.php");
$story = $_GET["story"];
$city = $_GET["city"];
$cnt = $_GET["page"];
$currentRows = 0;
$conn = connect();

function createButtons($conn)
{
    global $cnt;
    global $currentRows;
    global $story;
    global $city;
    $items = $cnt * 12;
    $sql = "select Category.name
			from (
			select CategoryMain.main_id as y
			from CategoryMain
			group by CategoryMain.main_id
			) as x, Category, City, Restaurant
			where Category.id=x.y and Restaurant.category_id=Category.id and City.name='$city' and Restaurant.city_id=City.id
			group by Category.name
			limit 12
			offset $items";

    $result = $conn->query($sql);
    $numRows = $result->num_rows;
    $currentRows = $cnt * 12 + $numRows;
    $cnt++;
    if ($numRows > 0) {
        // output data of each row
        for ($i = 1; $i <= 12 and $result->num_rows >= $i; $i++) //while($row = $result->fetch_assoc())
        {
            $row = $result->fetch_assoc();
            $url = "'" . 'badget.php?story=' . $story . '&city=' . $city . '&category=' . $row["name"] . "'";
            echo '<input style="width: 300px; 
							padding: 30px; 
							margin: 10px;
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
							id= "' . $i . '"
							value= "' . str_replace(' Restaurant', '', trim($row["name"])) . '"
							onclick="window.location.href=' . $url . '" />';
        }
    }
}

createButtons($conn);
?>

</br></br>

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
       onclick="history.go(-1);"/>

<?php
$sql2 = "select COUNT(catgory_per_city) as total
			from (
			select Category.name as catgory_per_city
			from (
			select CategoryMain.main_id as y
			from CategoryMain
			group by CategoryMain.main_id
			) as x, Category, City, Restaurant
			where Category.id=x.y and Restaurant.category_id=Category.id and City.name='$city' and Restaurant.city_id=City.id
			group by Category.name
			) as z";
$result2 = $conn->query($sql2);
$row = $result2->fetch_assoc();
$totalRows = $row['total'];
if ($currentRows < $totalRows) {
    ?>
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
           value="more categories"
           align="center"
           onclick="window.location.href='categories.php?story=<?php echo $story ?>&city=<?php echo $city ?>&page=<?php echo $cnt ?>'"/>
    <?php
}
?>
<?php $conn->close(); ?>
</body>
</html>
