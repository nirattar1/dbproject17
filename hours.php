

<!DOCTYPE html>
<html>
	
<head>
<style>
p {
    font-family: "Comic Sans MS", cursive, sans-serif;
	text-align: center;
	font-size: 250%;
	
}



</style>

</head>
<body>
<p>choose your favorite hours.. </p>

<?php 
$category= $_GET["category"];
?>

</br>
</br>
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
				value= "next"
				align="center"
				onclick="window.location.href='http://www.cs.tau.ac.il/~amitchen/citys.php?category=<?php echo $category ?>&badget=<?php echo "junk" ?>'" />	

				


</body>
</html>
