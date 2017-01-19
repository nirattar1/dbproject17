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
<p>Choose City</p>
<h1>Press on the city in which you want to find restuarents </h1>

<?php
require_once("connectToDB.php");
$story = $_GET["story"];
$currentRows = 0;
$cnt = $_GET["page"];
$conn = connect();

function createButtons($conn)
{
    global $cnt;
    global $currentRows;
    global $story;
    $items = $cnt * 12;
    $sql = "SELECT name FROM City LIMIT 12 OFFSET $items";

    $result = $conn->query($sql);
    $numRows = $result->num_rows;

    $currentRows = $cnt * 12 + $numRows;
    $cnt++;

    if ($numRows > 0) {
        // output data of each row
        for ($i = 1; $i <= 12 and $result->num_rows >= $i; $i++) //while($row = $result->fetch_assoc())
        {
            $row = $result->fetch_assoc();
            if ($story == 1) {
                $url = "'" . 'hours.php?story=' . $story . '&city=' . $row["name"] . "'";
            }
            if ($story == 3) {
                $url = "'" . 'categories.php?story=' . $story . '&city=' . $row["name"] . "'";
            }
            if ($story == 4) {
                $url = "'" . 'dish.php?story=' . $story . '&city=' . $row["name"] . "'";
            }
            if ($story == 5) {
                $url = "'" . 'expensive.php?story=' . $story . '&city=' . $row["name"] . "'";
            }
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
$sql2 = "SELECT COUNT(*) as total FROM City";
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
           value="more cities"
           align="center"
           onclick="window.location.href='citys.php?story=<?php echo $story ?>&page=<?php echo $cnt ?>'"/>
    <?php
}

if ($currentRows == $totalRows) {
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
       value="the city is not in the list.."
       align="center"
       onclick="window.location.href='newCity.php?story=<?php echo $story ?>&badget=<?php echo $badget ?>&page=<?php echo $cnt ?>'"/>
       
 <?php
} ?>


<?php $conn->close(); ?>
</body>
</html>
