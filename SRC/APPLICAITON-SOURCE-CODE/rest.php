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
            background-color: #8fbc8f;
        "; clear: left;
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
require_once("connectToDB.php");
$rest = $_GET["rest"];
$id = $_GET["id"];
$city = $_GET["city"];

$conn = connect();
$sql = "select * from Restaurant where id=\"$id\" limit 1";
$result = $conn->query($sql);
if ($result->num_rows > 0)
    $row = $result->fetch_assoc();
?>

<div class="container">
    <header>
        <h1><?php echo $rest; ?></h1>
    </header>
    <nav>
        <button id="Get current CheckIns count" value=<?php echo $row["checkinsCount"]; ?> onclick="checkinFunction()">
            Get the Check-Ins count
        </button>
        <p id="demo"></p>
        <script>
            function checkinFunction() {
                var x = document.getElementById("Get current CheckIns count").value;
                document.getElementById("demo").innerHTML = x;
            }
        </script>
    </nav>

    <nav>
        <button id="Get current users count" value=<?php echo $row["usersCount"]; ?> onclick="usersFunction()">Get the
            users count
        </button>
        <p id="demo2"></p>
        <script>
            function usersFunction() {
                var x = document.getElementById("Get current users count").value;
                document.getElementById("demo2").innerHTML = x;
            }
        </script>
    </nav>

    <article>
        <h1><?php echo $rest; ?></h1>
        <p>Address: <?php echo $row["address"] . ", " . $city; ?> </p>
        <p>URL: <a href="<?php echo $row["url"]; ?>"> <?php echo $row["url"]; ?> </a></p>
        <p>Phone: <?php echo $row["phone"]; ?> </p>
        <p>Open Hours: </p>
    </article>

</div>

</br></br>
<p align="center">
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
           onclick="history.go(-1);"/>
</p>
<?php $conn->close(); ?>
</body>
</html>
