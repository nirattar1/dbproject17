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

        table {
            border-spacing: 0.5rem;
            margin-left: 30%;
            margin-right: 15%;
        }

        tr {
            font-family: "Comic Sans MS", cursive, sans-serif;
            font-size: 100%;
            padding: 0.5rem;
        }

        tr:nth-child(6n+0) {
            background: hsl(150, 50%, 50%);
        }

        tr:nth-child(6n+1) {
            background: hsl(160, 60%, 50%);
        }

        tr:nth-child(6n+2) {
            background: hsl(170, 70%, 50%);
        }

        tr:nth-child(6n+3) {
            background: hsl(180, 80%, 50%);
        }

        tr:nth-child(6n+4) {
            background: hsl(190, 90%, 50%);
        }

        tr:nth-child(6n+5) {
            background: hsl(200, 99%, 50%);
        }
    </style>

</head>
<body>
<p> Celebrate special event?<br /> Want to impress her with a fancy restaurant?<br /> Let us help you <p>
<?php $city = $_GET["city"];
//city is the user's choice from previous page.
 ?>
<h1> * The most expensive dishes/drinks in <?php echo(str_replace('_', ' ', $city)) ?> *</h1>

<?php
require_once("connectToDB.php");
$conn = connect();
//get the city_id
$sql0 = "SELECT c.id AS id from City AS c WHERE c.name='$city'";
$result0 = $conn->query($sql0);
$row0 = $result0->fetch_assoc();
$city_id = $row0["id"];

//select the most expensive dish in the city. take maximun 10 dishes with the maximal price.
$sql = "
SELECT DISTINCT d.name, r.name AS rest, d.price, r.id AS r_id 
FROM Dish d, Restaurant r ,(
	SELECT max(d.price) as price
	FROM Dish d , Restaurant r
	WHERE r.id=d.restaurant_id AND r.city_id=$city_id) as t
WHERE d.price=t.price AND r.id=d.restaurant_id AND r.city_id=$city_id
ORDER BY d.name
LIMIT 10
;";
$result = $conn->query($sql);
?>

<table>
    <tr>
        <td></td>
        <td><b>Dish Name</b></td>
        <td><b>Restaurant Name</b></td>
        <td><b>Price</b></td>
    </tr>
	
    <?php
	// build a table with maximum 10 dishes, that has the maximal price
	for ($i = 0; $i < $result->num_rows; $i++) { ?>
	
        <tr>
            <td> <?php echo $i + 1; ?> </td>
            <?php $row = $result->fetch_assoc(); ?>
            <td> <?php echo $row["name"]; ?> </td>
            <td>
                <a href="rest.php?id=<?php echo $row["r_id"]; ?>"> <?php echo $row["rest"]; ?></a>
            </td>
            <td> <?php echo $row["price"]; ?> </td>
        </tr>
        <?php
    } ?>

</table>

</br>
</br>
</br>
</br>

<?php
//select the most expensive restaurant in the city, by average of the dish prices, 
//then select the most expensive dish in each section of the menu.
$sql2 = "
SELECT expensive_rest.name AS r_name, Dish.section_name, Dish.name AS d_name, max(Dish.price) AS max_p, expensive_rest.r_id
FROM Dish, (
			SELECT rest_prices.name,rest_prices.id AS r_id, max(rest_prices.price)
			FROM Restaurant AS r, Dish AS d, (
												SELECT r.name,r.id, avg(d.price) AS price
												FROM Restaurant AS r, Dish AS d
												WHERE r.id= d.restaurant_id AND r.city_id=$city_id AND d.price IS NOT NULL
												GROUP BY r.id) AS rest_prices
			WHERE r.id= d.restaurant_id AND r.city_id=$city_id AND d.price IS NOT NULL ) AS expensive_rest
WHERE Dish.restaurant_id= expensive_rest.r_id AND Dish.price IS NOT NULL
GROUP BY Dish.section_name";

$result2 = $conn->query($sql2);
$row2 = $result2->fetch_assoc();
?>

<h1> * The most expensive restaurant in <?php echo(str_replace('_', ' ', $city)) ?> is <a
        href="rest.php?id=<?php echo $row2["r_id"]; ?>"><?php echo $row2["r_name"] ?> </a>*
</h1>

<h1>the most expensive dishes in <?php echo $row2["r_name"] ?> are: </h1>

<table>
    <tr>
        <td></td>
        <td><b>Section</b></td>
        <td><b>Dish Name</b></td>
        <td><b>Price</b></td>
    </tr>

    <tr>
        <td> <?php echo 1; ?> </td>
        <td> <?php echo $row2["section_name"]; ?> </td>
        <td> <?php echo $row2["d_name"]; ?> </td>
        <td> <?php echo $row2["max_p"]; ?> </td>
    </tr>
    <?php for ($j = 1; $j < $result2->num_rows; $j++) { ?>
        <tr>
            <td> <?php echo $j + 1; ?> </td>
            <?php $row2 = $result2->fetch_assoc(); ?>
            <td> <?php echo $row2["section_name"]; ?> </td>
            <td> <?php echo $row2["d_name"]; ?> </td>
            <td> <?php echo $row2["max_p"]; ?> </td>
        </tr>
        <?php
    } ?>

</table>

</br></br>
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

<?php $conn->close(); ?>

</body>
</html>
