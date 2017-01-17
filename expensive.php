

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
<p> 1. The most expensive dishes/drinks in <?php echo (str_replace('_',' ',$city)) ?> </p>

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

$sql0= "select c.id as id from City as c where c.name='$city'";
$result0 = $conn->query($sql0);
$row0 = $result0->fetch_assoc(); 
$city_id= $row0["id"];


$sql = "select distinct d.name, r.name as rest, d.price, r.id as r_id 
from Dish d, Restaurant r , (select max(d.price) as price
from Dish d , Restaurant r
where r.id=d.restaurant_id and r.city_id=$city_id) as t
where d.price=t.price and r.id=d.restaurant_id and r.city_id=$city_id
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
            <td><a href="rest.php?rest=<?php echo $row["rest"]; ?>&id=<?php echo $row["r_id"]; ?>&city=<?php echo $city; ?>"> <?php echo $row["rest"]; ?></a> </td>
            <td> <?php echo $row["price"]; ?> </td>
        </tr>
        <?php
    }?>

</table>

</br>
</br>
</br>
</br>

<?php 
$sql2 = "select expensive_rest.name as r_name, Dish.section_name, Dish.name as d_name, max(Dish.price) as max_p, expensive_rest.r_id
		from Dish, (
					select rest_prices.name,rest_prices.id as r_id, max(rest_prices.price)
					from Restaurant as r, Dish as d, (
															select r.name,r.id, avg(d.price) as price
															from Restaurant as r, Dish as d
															where r.id= d.restaurant_id and r.city_id=$city_id and d.price is not NULL
															group by r.id) as rest_prices
					where r.id= d.restaurant_id and r.city_id=$city_id and d.price is not NULL ) as expensive_rest
where Dish.restaurant_id= expensive_rest.r_id and Dish.price is not NULL
group by Dish.section_name";

$result2 = $conn->query($sql2);
$row2 = $result2->fetch_assoc();
?>

<p> 2. The most expensive restaurant in <?php echo (str_replace('_',' ',$city)) ?> is <a href="rest.php?rest=<?php echo $row2["r_name"]; ?>&id=<?php echo $row2["r_id"]; ?>&city=<?php echo $city; ?>"><?php echo $row2["r_name"] ?> </a></p>

<p>the most expensive dishes in <?php echo $row2["r_name"] ?> are: </p>

<table>
    <tr>
        <td> </td>
        
        <td>Section</td>
        <td>Dish Name</td>
        <td>Price</td>
    </tr>
    
		<tr>
			<td> <?php echo 1; ?> </td>
            <td> <?php echo $row2["section_name"]; ?> </td>
            <td> <?php echo $row2["d_name"]; ?> </td>
            <td> <?php echo $row2["max_p"]; ?> </td>
        </tr>
    <?php for($j=1; $j< $result2->num_rows ; $j++) { ?>
        <tr>
            <td> <?php echo $j + 1; ?> </td>
            <?php $row2 = $result2->fetch_assoc(); ?>
            <td> <?php echo $row2["section_name"]; ?> </td>
            <td> <?php echo $row2["d_name"]; ?> </td>
            <td> <?php echo $row2["max_p"]; ?> </td>
        </tr>
        <?php
    }?>

</table>

<?php $conn->close(); ?>

</body>
</html>
