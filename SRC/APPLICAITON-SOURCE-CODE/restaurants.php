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
<p>Here are the best matches to your search criteria:</p>
<h1>the result order is based on the popularity of the place</h1>
<?php
require_once("connectToDB.php");
$city= $_GET["city"];
$category= $_GET["category"];
$badget= $_GET["badget"];
$story= $_GET["story"];
$from_hour= $_GET["from_hour"];
$to_hour= $_GET["to_hour"];
$day= $_GET["day"];
$from;
$to;

if ($badget==1){
	$from=0;
	$to=10;
}
else if ($badget==2){
	$from=10;
	$to=50;
}
else if ($badget==3){
	$from=50;
	$to=10000;
}

$conn = connect(); 
if($story==3) {
	
$sql1 = "select name, restaurant_avg, id
from (
select restaurant_table.name as name, restaurant_table.id as id, avg(restaurant_table.section_avg) as restaurant_avg
from (
select section_table.name, section_table.id, section_table.section_name , 
avg(section_table.price) as section_avg 
from (
select Restaurant.name, Restaurant.id , Dish.section_name , Dish.name as name1  , Dish.price
from Restaurant , City , Category , Dish
where  City.name='$city' and Category.name='$category' and City.id=Restaurant.city_id and 
Restaurant.category_id=Category.id and Restaurant.has_menu=1 and Dish.restaurant_id=Restaurant.id
) as section_table
group by section_table.section_name
) as restaurant_table
group by restaurant_table.id desc
) as avg_by_budget
where $from < restaurant_avg and restaurant_avg < $to
limit 15";
}

else if($story==1){
	$str="";
	if ($from_hour != "-1") {
		$str=$str." and o.open_hour<=\"".$from_hour.":00\"";
		if ($to_hour == "-1") {
			$str=$str." and o.close_hour>=\"".$from_hour.":00\"";
		}
	}
	if ($to_hour != "-1") {
		$str=$str." and o.close_hour>=\"".$to_hour.":00\"";
		if ($from_hour == "-1") {
			$str=$str." and o.open_hour<=\"".$to_hour.":00\"";
		}
	}
	if($day!="-1") {
		$str=$str." and o.day=".$day;
	}

	$sql1 = "select r.name, r.checkinsCount , r.id
					FROM Restaurant r
					WHERE EXISTS 
  					(SELECT * FROM City c, OpenHours o 
   					  where c.name='$city'
					  and c.id=r.city_id 
					  and r.id=o.restaurant_id $str)
   					  order by r.checkinsCount
   					  desc limit 15";
	}


$result = $conn->query($sql1);

if ( $result->num_rows > 0 ){ 
if($story==3){	?>	
<table>
	<tr>
		<td><b>Popolarity Number</b> </td>
		<td><b>Restaurant Name</b></td>
		<td><b>Restaurant average price for dish</b></td>
	</tr>
	
	<?php for($i=0; $i< $result->num_rows ; $i++){?>
	<tr>
			<td> <?php echo $i+1; ?> </td>
			<?php  $row = $result->fetch_assoc(); ?>
			<td><a href="rest.php?id=<?php echo $row["id"]; ?>"> <?php echo $row["name"]; ?> </a></td>
			<td> <?php echo $row["restaurant_avg"]; ?> </td>
	</tr>
<?php } ?>	
</table>
<?php } ?>
<?php if($story==1){	?>
<table>
	<tr>
		<td><b>Popolarity Number</b> </td>
		<td><b>Restaurant Name</b></td>
		<td><b>Checkins Count</b></td>
	</tr>
	
	<?php for($i=0; $i< $result->num_rows ; $i++){?>
	<tr>
			<td> <?php echo $i+1; ?> </td>
			<?php  $row = $result->fetch_assoc(); ?>
			<td><a href="rest.php?id=<?php echo $row["id"]; ?>"> <?php echo $row["name"]; ?> </a></td>
			<td> <?php echo $row["checkinsCount"]; ?> </td>
	</tr>
<?php } ?>	
</table>
<?php } ?>
<?php } ?>

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
		   onclick="history.go(-1);"/>
</h3>

<?php $conn->close(); ?>

</body>
</html>
