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
<p>Hungry?</p>
<h1>Let us help you find the best place to eat based on your preferences</h1>

<?php
$story;
?>

<script>
    function submitStory(value) {
        if (value == 1) {
            var link_params = "citys.php?story=1";
            window.location.href = link_params;

        }
        if (value == 2) {
            var link_params = "popularty.php?story=2";
            window.location.href = link_params;
        }
        if (value == 3) {
            var link_params = "citys.php?story=3";
            window.location.href = link_params;
        }
        if (value == 4) {
            var link_params = "citys.php?story=4";
            window.location.href = link_params;
        }
        if (value == 5) {
            var link_params = "citys.php?story=5";
            window.location.href = link_params;
        }

    }
</script>

</br>
</br>
<h2 align="center">
<input style="text-align: center 
				width: 100px; 
				height: 200px;
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
				font-size: 100%;"
       type="button"
       value="Want to go on specific time or date"
       align="center"
       onclick="submitStory(1)"/>


<input style="text-align: center
				width: 100px; 
				height: 200px;
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
				font-size: 100%;"
       type="button"
       value="Find the most popular places to eat"
       align="center"
       onclick="submitStory(2)"/>

<input style="text-align: center
				width: 100px;
				height: 200px; 
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
				font-size: 100%;"
       type="button"
       value="Not sure where and what you want?" ;
       align="center"
       onclick="submitStory(3)"/>
</br>
</br>
<input style="text-align: center 
				width: 100px; 
				height: 200px;
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
				font-size: 100%;"
       type="button"
       value="Know which dish you want to eat?"
       align="center"
       onclick="submitStory(4)"/>

<input style="text-align: center
				width: 100px; 
				height: 200px;
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
				font-size: 100%;"
       type="button"
       value="Wanna spend more money then usual?"
       align="center"
       onclick="submitStory(5)"/>
</h2>

</body>
</html>
