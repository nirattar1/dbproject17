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
<body style="background-image:url(background.jpg)">

<?php
require_once("connectToDB.php");
$id = $_GET["id"];

$conn = connect();
/*the query takes all the restaurant info about specific restaurant*/
$sql = "select * from Restaurant where id=\"$id\" limit 1";
$result = $conn->query($sql);
if ($result->num_rows > 0)
    $row = $result->fetch_assoc();
/*the query takes the open hours of specific restaurant*/
$sql2 = "select * from OpenHours where  restaurant_id =\"$id\" order by day limit 100";
$hour_result = $conn->query($sql2);
$city=$row["city_id"];
/*the query takes the city name of specific restaurant*/
$sql3 = "select * from City where  id =\"$city\" limit 1";
$city_result = $conn->query($sql3);
if ($city_result->num_rows > 0)
    $city_row = $city_result->fetch_assoc();
$category=$row["category_id"];
/*the query takes the category name of specific restaurant*/
$sql4 = "select * from Category where  id =\"$category\" limit 1";
$category_result = $conn->query($sql4);
if ($category_result->num_rows > 0)
    $category_row = $category_result->fetch_assoc();
?>

<div class="container">
    <header>
        <h1><?php echo $row["name"]; ?></h1>
    </header>

    <nav>
        <p><b>Different users count:</b><br><br> <?php echo $row["usersCount"]; ?> </p>
    </nav>

    <nav>
        <p><b>Check-Ins count:</b><br><br> <?php echo $row["checkinsCount"]; ?> </p>
    </nav>

    <article>
        <h1><?php echo $rest; ?></h1>
        <p><b>Address:</b> <?php echo $row["address"] . ", " . $city_row["name"]; ?> </p>
        <p><b>URL:</b> <a href="<?php echo $row["url"]; ?>"> <?php echo $row["url"]; ?> </a></p>
        <p><b>Phone:</b> <?php echo $row["phone"]; ?> </p>
        <p><b>Category:</b> <?php echo $category_row["name"]; ?> </p>
        <p><b>Open Hours:</b><br>
            <?php
            $days=array(
                1=>"Sunday",
                2=>"Monday",
                3=>"Tuesday",
                4=>"Wednesday",
                5=>"Thursday",
                6=>"Friday",
                7=>"Saturday");
            for($i=0; $i< $hour_result->num_rows ; $i++) {
                $hour_row = $hour_result->fetch_assoc();
                if (array_key_exists($hour_row["day"], $days))
                    echo $days[$hour_row["day"]] . ": " . substr($hour_row["open_hour"],0,5) . "-" . substr($hour_row["close_hour"],0,5);
                ?>
            <br>
            <?php
            }?></p>
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
           value="new search"
           align="center"
           onclick="window.location.href='welcome.php'"/>
</p>
<?php $conn->close(); ?>
</body>
</html>
