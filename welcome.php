

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
<p>Hungry?</p>

<?php 
$story;
?>

<script> 
function submitStory(value) 
{
  if (value==1){
    var link_params = "citys.php?story=1";
    window.location.href = link_params;
    
  }
  if (value==2){
    var link_params = "popularty.php?story=2";
    window.location.href = link_params;
  }
   if (value==3){
     var link_params = "categories.php?story=3";
      window.location.href = link_params;
  }
   if (value==4){
   var link_params = "citys.php?story=4";
    window.location.href = link_params;
  }
   if (value==5){
   var link_params = "citys.php?story=5";
    window.location.href = link_params;
  }
   if (value==6){
   var link_params = "???.php?story=6";
    window.location.href = link_params;
  }

}
</script>

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
				font-size: 150%;" 
				type="button" 
				value= "are you a night owl? &#x00A; or a morning person?..."
				align="center"
        onclick="submitStory(1)" />	
        
				
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
				font-size: 150%;" 
				type="button" 
				value= "popularty is your thing??..&#x00A; find the most popular &#x00A; places to eat"
				align="center"		
        onclick="submitStory(2)" />
				
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
				font-size: 150%;" 
				type="button" 
				value= "need guidance?";
				align="center"
        onclick="submitStory(3)" />
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
				font-size: 150%;" 
				type="button" 
				value= "already know what &#x00A; you want to eat?? &#x00A; find where!" 
				align="center"
        onclick="submitStory(4)" />
				
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
				font-size: 150%;" 
				type="button" 
				value= "got some dollars &#x00A in you pocket?"
				align="center"
        onclick="submitStory(5)" />	


</body>
</html>
