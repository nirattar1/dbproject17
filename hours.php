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
	text-align: center 
	width: 170px; 
	padding: 30px; 
	margin: auto;
	position: center;
	font-weight: bold; 
	font-size: 150%;
}
select {
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
input{
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
<p>choose your favorite hours.. </p>

<?php 
$city= $_GET["city"];
?>

<h1>From hour:</h1>
<select id="from_hour">	
    <option value="-1">No preference</option>
	<?php
		for($i=0; $i<=23 ; $i++)
		{
			echo '<option value="'.$i.'">'.$i.':00</option>';
		}
	?>
</select>


<h1>To hour:</h1>
<select id="to_hour">	
    <option value="-1">No preference</option>
	<?php
		//second list starts one hour later
		for($i=1; $i<=24 ; $i++)
		{
			$val = $i % 24;     //fix 24 to 0
			echo '<option value="'.$val.'">'.$val.':00</option>';
		}
	?>
</select>


</br>
</br>

<script> 
function submitHoursData() 
{
	//prepare link containing hours from controls
	var link_params = "http://www.cs.tau.ac.il/~amitchen/restaurants.php";
	var e1 = document.getElementById("from_hour");
	var strFromHour = e1.options[e1.selectedIndex].value;
	var e2 = document.getElementById("to_hour");
	var strToHour = e2.options[e2.selectedIndex].value;	
	link_params = link_params.concat("?");
	link_params = link_params.concat("city=<?php echo $city ?>");
	link_params = link_params.concat("&from_hour=",strFromHour);
	link_params = link_params.concat("&to_hour=",strToHour);
		
	//jump to link
	window.location.href = link_params;
}
</script>
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
				value= "back"
				align="center"
				onclick="history.go(-1);" />
				
<input type="button" value= "next" align="center" onclick="submitHoursData()">
			
</body>
</html>
