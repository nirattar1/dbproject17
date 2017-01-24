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
            text-align: center;
            font-size: 150%;
        }
    </style>

</head>
<body style="background-image:url(background.jpg)">

<p>What is your budget?</p>
<h1>Choose a range for the average price you are willing to pay for a meal </h1>

<?php

// Save starting page, city and category the user chosen

$story = $_GET["story"];
$city = $_GET["city"];
$category = $_GET["category"];


//button of badget and back button
?>

</br>
</br>
<h2 align="center">
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
       value="up to 10$"
       align="center"
       onclick="window.location.href='restaurants.php?story=<?php echo $story ?>&city=<?php echo $city ?>&category=<?php echo $category ?>&badget=<?php echo "1" ?>'"/>

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
       value="10$ to 50$"
       align="center"
       onclick="window.location.href='restaurants.php?story=<?php echo $story ?>&city=<?php echo $city ?>&category=<?php echo $category ?>&badget=<?php echo "2" ?>'"/>

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
       value="more then 50$"
       align="center"
       onclick="window.location.href='restaurants.php?story=<?php echo $story ?>&city=<?php echo $city ?>&category=<?php echo $category ?>&badget=<?php echo "3" ?>'"/>
</br>
</br>
</h2>
<h2 align="center">
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
       onclick="window.location.href='categories.php?story=<?php echo $story ?>&city=<?php echo $city ?>'"/>
</h2>
</body>
</html>
