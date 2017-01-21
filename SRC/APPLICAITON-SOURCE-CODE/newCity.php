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
<body style="background-image:url(background.jpg)">
<p>Add City</p>


<?php
$story = $_GET["story"];
$city_name = $_POST['txt_city'];
//require_once("connectToDB.php");
require_once("API-DATA-RETRIVAL/5_add_new_city.php");
//$conn = connect();
?>

<h1>Enter a city name:</h1>

<h2 align="center">
<form method="post">
    <input type="text" name="txt_city">
    </br>
    <input type="submit" value="Let's go!" name="btn_go">
</form>
    </h2>
<?php    

if (isset($_POST['btn_go'])) {
    if (!empty($city_name)) {
        $errorStr = addNewCityStory($cityName);
        echo $errorStr;
        if($errorStr !== true){
            echo '<script language="javascript">';
            echo 'alert('.$errorStr.')';
            echo '</script>';
        }
    }
}
?>
    
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
