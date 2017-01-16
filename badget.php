

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
<p>What is your budget?</p>

<?php 
$category= $_GET["category"];
$story= $_GET["story"];
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
				value= "Junk (10$ to 30$)"
				align="center"
				onclick="window.location.href='http://www.cs.tau.ac.il/~kobihazan/citys.php?story=<?php echo $story ?>&category=<?php echo $category ?>&badget=<?php echo "junk" ?>'" />	
				
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
				value= "regular (30$ to 50$)"
				align="center"
				onclick="window.location.href='http://www.cs.tau.ac.il/~kobihazan/citys.php?story=<?php echo $story ?>&category=<?php echo $category ?>&badget=<?php echo "regular" ?>'" />	
				
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
				value= "fancy(50$ and higher)"
				align="center"
				onclick="window.location.href='http://www.cs.tau.ac.il/~kobihazan/citys.php?story=<?php echo $story ?>&category=<?php echo $category ?>&badget=<?php echo "fancy" ?>'" />	
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
				value= "back"
				align="center"
				onclick="window.location.href='http://www.cs.tau.ac.il/~kobihazan/???'" />
				


</body>
</html>
