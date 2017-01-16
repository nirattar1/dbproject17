

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
<p>single rest </p>


<?php 
$rest= $_GET["rest"];
echo $rest;
function connect()
{
	$servername = "mysqlsrv.cs.tau.ac.il";
	$username = "DbMysql07";
	$password = "DbMysql07";
	$dbname = "DbMysql07";
	
	// $servername = "localhost";
	// $username = "root";
	// $password = "";
	// $dbname = "dbmysql07_local";
	
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

</body>
</html>
