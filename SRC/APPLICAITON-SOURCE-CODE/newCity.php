<!DOCTYPE html>
<html>

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

    .alert {
        padding: 20px;
        background-color: #f44336; /* Red */
        color: white;
        margin-bottom: 15px;
    }

    /* The close button */
    .closebtn {
        margin-left: 15px;
        color: white;
        font-weight: bold;
        float: right;
        font-size: 22px;
        line-height: 20px;
        cursor: pointer;
        transition: 0.3s;
    }

    /* When moving the mouse over the close button */
    .closebtn:hover {
        color: black;
    }

</style>

<?php
$story = $_GET["story"];
$city_name = $_POST['txt_city'];
$pattern = '/[A-Za-z]+/';
if (isset($_POST['btn_go'])) {
    if (!empty($_POST['txt_city'])) {
        if (ctype_alpha($city_name) == 1) {
            echo $city_name;
        } else {
            //echo "error!";
            echo '<script language="javascript">';
            echo 'alert("aaaaammmmiiiittttt!!!!!")';
            echo '</script>';
        }
    }
}

?>

<body style="background-image:url(background.jpg)">
<p>Choose City</p>


<?php
require_once("connectToDB.php");
$currentRows = 0;
$badget = $_GET["badget"];
$category = $_GET["category"];
$conn = connect();
?>

<h1>Enter a city name:</h1>
<h2 align="center">
<form method="post">
    <input type="text" name="txt_city">
    </br>
    <input type="submit" value="Let's go!" name="btn_go">
</form>
    </h2>
</br>
<h2 align="center">
<h3 align="center">
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
</h3>
</h2>

<?php $conn->close(); ?>
</body>
</html>
