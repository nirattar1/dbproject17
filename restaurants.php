<!DOCTYPE html>
<html>
	
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
	
<body>
<p>Your Best Match:</p>
<?php
$badget= $_GET["badget"];
$category= $_GET["category"];
$city= $_GET["city"];

function connect(){
	$servername = "mysqlsrv.cs.tau.ac.il";
	$username = "DbMysql07";
	$password = "DbMysql07";
	$dbname = "DbMysql07";
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
$sql = "select Restaurant.name, Restaurant.checkinsCount , Restaurant.id from Restaurant order by Restaurant.checkinsCount desc limit 15";
$result = $conn->query($sql);
?>

<table>
	<tr>
		<td>Popolarity Number </td>
		<td>Restaurant Name</td>
		<td>Checkins Count</td>
	</tr>
	
	<?php for($i=0; $i< $result->num_rows ; $i++){?>
	<tr>
			<td> <?php echo $i+1; ?> </td>
			<?php  $row = $result->fetch_assoc(); ?>
			<td><a href="rest.php?rest=<?php echo $row["name"]; ?>&id=<?php echo $row["id"]; ?>&city=<?php echo $city; ?>"> <?php echo $row["name"]; ?> </a></td>
			<td> <?php echo $row["checkinsCount"]; ?> </td>
	</tr>
		<?php 
	} ?>	

</table>
<?php $conn->close(); ?>
</body>
</html>
