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
	
<body style="background-image:url(background.jpg)">
<p>Here are the best matches to your search criteria:</p>
<h1>the result order is based on the popularity of the place</h1>
<?php

require_once("connectToDB.php");

//get category name. returns category Id.
//will return -1 on failure.
function getCategoryIdByName($conn, $category)
{
    $sql = "SELECT id FROM Category WHERE name='$category'";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        return $row['id'];
    } else {
        return -1;
    }
}

// Save starting page, city, category, budget, hours and days the user chosen

$city= $_GET["city"];
$category= $_GET["category"];
$badget= $_GET["badget"];
$story= $_GET["story"];
$from_hour= $_GET["from_hour"];
$to_hour= $_GET["to_hour"];
$day= $_GET["day"];
$from;
$to;




// Update budget range according to the information from previous page in case the story = 3
if($story==3) {
	
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
}

$conn = connect(); 
if($story==3) {

# Query - popular restaurants according to $city, $category and $budget by the user choice. 
## restaurants in the budget range if average menu in the range. subcategories of catgory considered in $category and therefore will be shown in the result.

//first resolve category id from name.
$cat_id = getCategoryIdByName($conn, $category);
	
$sql1 = "
SELECT table3.id
,table3.name
,table3.Category_name
	,table3.checkinsCount
	,restaurant_avg
FROM (
	SELECT table2.id
 ,table2.name
 ,table2.Category_name
		,table2.checkinsCount
		,avg(table2.section_avg) AS restaurant_avg
	FROM (
		SELECT table1.id
   ,table1.name
   ,table1.Category_name
			,table1.checkinsCount
			,table1.section_name
			,avg(table1.price) AS section_avg
		FROM (
			SELECT Restaurant.id
      ,Restaurant.name
      ,Category.name as Category_name
				,Restaurant.checkinsCount
				,Dish.section_name
				,Dish.price
			FROM Restaurant
				,City
				,Category
				,Dish
        ,( select x.id as main_id, y.id as sub_id
        from CategoryMain, Category as x, Category as y
        where CategoryMain.main_id=x.id and x.id='$cat_id' and CategoryMain.category_id=y.id )as sub_catgoryes
			WHERE City.NAME = '$city'
        AND City.id = Restaurant.city_id
				AND sub_catgoryes.main_id = '$cat_id'
        and sub_catgoryes.sub_id = Category.id
		    AND Category.id = Restaurant.category_id
				AND Restaurant.has_menu = 1
				AND Dish.restaurant_id = Restaurant.id
			) AS table1
		GROUP BY table1.section_name
		) AS table2
	GROUP BY table2.id DESC
 	order by table2.checkinsCount desc
	) AS table3
WHERE $from < restaurant_avg
	AND restaurant_avg < $to limit 15
  ";
}

// calculate SQL condition of opening hours and day by the user choice 

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

# Query - popular restaurants according to $city and opening hours and day requested by the user. 	
	
	$sql1 = "select r.name, r.address , r.checkinsCount , r.id
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

//Construction of the appropriate table

if ( $result->num_rows > 0 ){ 
if($story==3){	?>	
<table>
	<tr>
		<td><b></b> </td>
		<td><b>Restaurant Name</b></td>
   <td><b>Restaurant Catgory</b></td>
		<td><b>Checkins Count</b></td>
		<td><b>Restaurant average menu price</b></td>
	</tr>
	
	<?php for($i=0; $i< $result->num_rows ; $i++){?>
	<tr>
			<td> <?php echo $i+1; ?> </td>
			<?php  $row = $result->fetch_assoc(); ?>
			<td><a href="rest.php?id=<?php echo $row["id"]; ?>"> <?php echo $row["name"]; ?> </a></td>
          <td> <?php echo $row["Category_name"]; ?> </td>
      <td> <?php echo $row["checkinsCount"]; ?> </td>
			<td> <?php echo $row["restaurant_avg"]; ?> </td>
	</tr>
<?php } ?>	
</table>
<?php } ?>
<?php if($story==1){	?>
<table>
	<tr>
		<td><b></b> </td>
		<td><b>Restaurant Name</b></td>
   <td><b>Restaurant Addres</b></td>
		<td><b>Checkins Count</b></td>
	</tr>
	
	<?php for($i=0; $i< $result->num_rows ; $i++){?>
	<tr>
			<td> <?php echo $i+1; ?> </td>
			<?php  $row = $result->fetch_assoc(); ?>
			<td><a href="rest.php?id=<?php echo $row["id"]; ?>"> <?php echo $row["name"]; ?> </a></td>
      <td> <?php echo $row["address"]; ?> </td>
			<td> <?php echo $row["checkinsCount"]; ?> </td>
	</tr>
<?php } ?>	
</table>
<?php } ?>
<?php } 

//An error message if no results to display

if ( $result->num_rows == 0 ){
	if($story==3){
    echo '<script language="javascript">';
    echo 'alert("There are no restaurants in selected budget")';
    echo '</script>';  
?>   
    <meta http-equiv="refresh" content="0; url='badget.php?story=<?php echo $story ?>&city=<?php echo $city ?>&category=<?php echo $category ?>'"/>
<?php }
else if($story==1){
    echo '<script language="javascript">';
    echo 'alert("There are no restaurants in selected hours")';
    echo '</script>';  
?>   
    <meta http-equiv="refresh" content="0; url='hours.php?story=<?php echo $story ?>&city=<?php echo $city ?>'"/>
<?php }

//button to move the previous page  

 }?>

 
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
		   value="new search"
		   align="center"
		   onclick="window.location.href='welcome.php'"/>
</h3>

<?php $conn->close(); ?>

</body>
</html>
