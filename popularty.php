

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
<p>popularty is you thing???</p>

<?php 
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
	
	return $conn;
}
$conn = connect(); 
$sql = "SELECT c.name as city_name , r.name as rest_name,r.id as r_id , max(r.checkinsCount) as chekins, best_cat.cat_name
FROM Restaurant r, City c, Category cat,
(

select x.city_name, y.cat_name
from
 (select city_categories.city_name, max(city_categories.cnt) as m
 from
			(
			select distinct City.name as city_name, c.name as cat_name,count(c.name) as cnt
			from Restaurant r, Category c, City
			where r.categories = c.id and City.id= r.city_id
			group by  City.name,c.name
			) as city_categories

 group by city_categories.city_name) as x ,
 	(
			select distinct City.name as city_name, c.name as cat_name,count(c.name) as cnt
			from Restaurant r, Category c, City
			where r.categories = c.id and City.id= r.city_id
			group by  City.name,c.name
			) as y
where x.city_name= y.city_name and x.m= y.cnt
 

) as best_cat 
WHERE r.city_id=c.id and r.categories= cat.id and best_cat.city_name= c.name
GROUP BY c.name";
$result = $conn->query($sql);
?>

<table>
	<tr>
		<td>City </td>
		<td>The most popular restaurant</td>
		<td>The most popular category</td>
	</tr>
	
	<?php for($i=0; $i< $result->num_rows ; $i++){?>
	<tr>
		
			<?php  $row = $result->fetch_assoc(); ?>
			<td><?php echo $row["city_name"]; ?> </td>
			<td> <a href="rest.php?rest=<?php echo $row["rest_name"]; ?>&id=<?php echo $row["r_id"]; ?>&city=<?php echo $city; ?>"><?php echo $row["rest_name"]; ?> </a></td>
			<td> <?php echo $row["cat_name"]; ?> </td>
	</tr>
		<?php 
	} ?>	

</table>
<?php $conn->close(); ?>

</body>
</html>
