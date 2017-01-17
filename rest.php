<!DOCTYPE html>
<html>
<head>
    <style>
        div.container {
            width: 100%;
            border: 1px solid gray;
        }

        header {
            padding: 1em;
            color: #000000;
            background-color: #8fbc8f;";
            clear: left;
            text-align: center;
            font-family: "Comic Sans MS", cursive, sans-serif;
            text-align: center;
            font-size: 100%;
        }

        nav {
            float: right;
            max-width: 160px;
            margin: 0;
            padding: 2em;
            font-family: "Comic Sans MS", cursive, sans-serif;
            font-size: 130%;
            text-align: center;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
        }

        nav ul a {
            text-decoration: none;
        }

        article {
            margin-left: 30px;
            border-right: 1px solid gray;
            padding: 1em;
            overflow: hidden;
            font-family: "Comic Sans MS", cursive, sans-serif;
            font-size: 100%;
        }
    </style>
</head>
<body>

<?php

$rest= $_GET["rest"];
$id= $_GET["id"];
$city= $_GET["city"];
function connect()
{
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

$conn = connect();
$sql = "select * from Restaurant where id=\"$id\" limit 1";
$result = $conn->query($sql);
if($result->num_rows>0)
    $row = $result->fetch_assoc();


?>

<div class="container">
    <header>
        <h1><?php echo $rest;?></h1>
    </header>

    <nav>
                    <button id="Get current CheckIns count" value=<?php echo $row["checkinsCount"];?> onclick="checkinFunction()">Get current CheckIns count</button>
                    <p id="demo"></p>
                    <script>
                        function checkinFunction() {
                            var x = document.getElementById("Get current CheckIns count").value;
                            document.getElementById("demo").innerHTML = x;
                        }
                    </script>
    </nav>

    <nav>
                    <button id="Get current users count" value=<?php echo $row["usersCount"];?> onclick="usersFunction()">Get the current users count</button>
                    <p id="demo2"></p>
                    <script>
                        function usersFunction() {
                            var x = document.getElementById("Get current users count").value;
                            document.getElementById("demo2").innerHTML = x;
                        }
                    </script>
    </nav>

    <article>
        <h1><?php echo $rest;?></h1>
        <p>Address: <?php echo $row["address"]. ", ". $city;?> </p>
        <p>URL: <?php echo $row["url"];?> </p>
        <p>Phone: <?php echo $row["phone"];?> </p>
        <p>Open Hours: </p>
    </article>

</div>

</body>
</html>
