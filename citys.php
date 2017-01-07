<?php
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
echo "Connected successfully";
$sql = "SELECT name FROM city";
$result = $conn->query($sql);
$data = mysqli_fetch_array($result);

if ($result->num_rows > 0) {
     // output data of each row
     while($row = $result->fetch_assoc()) {
         echo "<br> name: ". $row["name"]. "<br>";
     }
} else {
     echo "0 results";
}

$conn->close();
?> 

<!DOCTYPE html>
<html>
<head>
<style>
p {
    font-family: "Comic Sans MS", cursive, sans-serif;
	text-align: center;
	font-size: 250%;
}
h2 {
	position: center;
}
</style>
</head>
<body>
<p>Choose city</p>
<form>

<h2>

<input style="width: 300px; 
padding: 30px; 
margin: 10px;
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
value="city 2" 
onclick="window.location.href='http://www.hyperlinkcode.com/button-links.php'" />

<input style="width: 300px; 
padding: 30px; 
margin: 10px;
cursor: pointer; 
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
value="city 2" 
onclick="window.location.href='http://www.hyperlinkcode.com/button-links.php'" />

<input style="width: 300px; 
padding: 30px; 
margin: 10px;
cursor: pointer; 
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
value="city 2" 
onclick="window.location.href='http://www.hyperlinkcode.com/button-links.php'" />

<input style="width: 300px; 
padding: 30px; 
margin: 10px;
cursor: pointer; 
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
value="city 2" 
onclick="window.location.href='http://www.hyperlinkcode.com/button-links.php'" />

<input style="width: 300px; 
padding: 30px; 
margin: 10px;
cursor: pointer; 
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
value="city 2" 
onclick="window.location.href='http://www.hyperlinkcode.com/button-links.php'" />

<input style="width: 300px; 
padding: 30px; 
margin: 10px;
cursor: pointer; 
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
value="city 2" 
onclick="window.location.href='http://www.hyperlinkcode.com/button-links.php'" />

<input style="width: 300px; 
padding: 30px; 
margin: 10px;
cursor: pointer; 
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
value="city 2" 
onclick="window.location.href='http://www.hyperlinkcode.com/button-links.php'" />

<input style="width: 300px; 
padding: 30px; 
margin: 10px;
cursor: pointer; 
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
value="city 2" 
onclick="window.location.href='http://www.hyperlinkcode.com/button-links.php'" />

<input style="width: 300px; 
padding: 30px; 
margin: 10px;
cursor: pointer; 
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
value="city 2" 
onclick="window.location.href='http://www.hyperlinkcode.com/button-links.php'" />

<input style="width: 300px; 
padding: 30px; 
margin: 10px;
cursor: pointer; 
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
value="city 2" 
onclick="window.location.href='http://www.hyperlinkcode.com/button-links.php'" />

<input style="width: 300px; 
padding: 30px; 
margin: 10px;
cursor: pointer; 
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
value="city 2" 
onclick="window.location.href='http://www.hyperlinkcode.com/button-links.php'" />

<input style="width: 300px; 
padding: 30px; 
margin: 10px;
cursor: pointer; 
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
value="city 2" 
onclick="window.location.href='http://www.hyperlinkcode.com/button-links.php'" />

</h2>





</form>


</body>
</html>




