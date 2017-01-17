

<!DOCTYPE html>
<html>
	
<head>
    <style>
        p {
            font-family: "Comic Sans MS", cursive, sans-serif;
            text-align: center;
            font-size: 250%;

        }
        table {
            border-spacing: 0.5rem;
            margin-left:30%;
            margin-right:15%;
        }
        tr {
            font-family: "Comic Sans MS", cursive, sans-serif;
            font-size: 100%;
            padding: 0.5rem;
        }

        tr:nth-child(6n+0) { background: hsl(150, 50%, 50%); }
        tr:nth-child(6n+1) { background: hsl(160, 60%, 50%); }
        tr:nth-child(6n+2) { background: hsl(170, 70%, 50%); }
        tr:nth-child(6n+3) { background: hsl(180, 80%, 50%); }
        tr:nth-child(6n+4) { background: hsl(190, 90%, 50%); }
        tr:nth-child(6n+5) { background: hsl(200, 99%, 50%); }
    </style>

</head>
<body>
<?php $city= $_GET["city"]; ?>
<p> The most expensive dishes in <?php echo (str_replace('_',' ',$city)) ?> </p>

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

$conn = connect();
$sql = "select distinct d.name, r.name as rest, d.price 
from Dish d, Restaurant r , (select max(d.price) as price
from Dish d , Restaurant r
where r.id=d.restaurant_id and r.city_id=3) as t
where d.price=t.price and r.id=d.restaurant_id and r.city_id=3
order by d.name
limit 10
;";
$result = $conn->query($sql);


?>


<table>
    <tr>
        <td> </td>
        <td>Dish Name</td>
        <td>Restaurant Name</td>
        <td>Price</td>
    </tr>
    <?php for($i=0; $i< $result->num_rows ; $i++) { ?>
        <tr>
            <td> <?php echo $i + 1; ?> </td>
            <?php $row = $result->fetch_assoc(); ?>
            <td> <?php echo $row["name"]; ?> </td>
            <td> <?php echo $row["rest"]; ?> </td>
            <td> <?php echo $row["price"]; ?> </td>
        </tr>
        <?php
    }?>

</table>

</body>
</html>
