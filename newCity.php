<!DOCTYPE html>
<html>
	
<style>
p {
    font-family: "Comic Sans MS", cursive, sans-serif;
	text-align: center;
	font-size: 250%;
	
}



</style>	

<?php 
$story= $_GET["story"];
$city_name= $_POST['txt_city'];
$pattern =  '/[A-Za-z]+/';
if(isset($_POST['btn_go']))
{
	if ( ! empty($_POST['txt_city'])){
		if ( ctype_alpha( $city_name ) == 1){
			echo $city_name;
		}
		else {
			echo "error!";
		}
	}
		
	
}


 
?>

<body>
<p>Choose City</p>


<?php
$currentRows=0;
$badget= $_GET["badget"];
$category= $_GET["category"];
$conn = connect(); 

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


?>

<h1>Enter a city name:</h1>		
<form method="post">			
<input type= "text" name= "txt_city" >
</br>			
<input type= "submit" value= "Let's go!" name="btn_go">		
</form>

<?php $conn->close(); ?>
</body>
</html>
