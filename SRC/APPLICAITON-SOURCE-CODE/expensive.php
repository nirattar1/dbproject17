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
<?php $city = $_GET["city"]; ?>
<h1> * The most expensive dishes/drinks in <?php echo(str_replace('_', ' ', $city)) ?> *</h1>

<?php
require_once("connectToDB.php");
$conn = connect();

$sql0 = "select c.id as id from City as c where c.name='$city'";
$result0 = $conn->query($sql0);
$row0 = $result0->fetch_assoc();
$city_id = $row0["id"];

$sql = "select distinct d.name, r.name as rest, d.price, r.id as r_id 
from Dish d, Restaurant r , (select max(d.price) as price
from Dish d , Restaurant r
where r.id=d.restaurant_id and r.city_id=$city_id) as t
where d.price=t.price and r.id=d.restaurant_id and r.city_id=$city_id
order by d.name
limit 10
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
    <?php for ($i = 0; $i < $result->num_rows; $i++) { ?>
        <tr>
            <td> <?php echo $i + 1; ?> </td>
            <?php $row = $result->fetch_assoc(); ?>
            <td> <?php echo $row["name"]; ?> </td>
            <td>
                <a href="rest.php?rest=<?php echo $row["rest"]; ?>&id=<?php echo $row["r_id"]; ?>&city=<?php echo $city; ?>"> <?php echo $row["rest"]; ?></a>
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
$sql2 = "select expensive_rest.name as r_name, Dish.section_name, Dish.name as d_name, max(Dish.price) as max_p, expensive_rest.r_id
		from Dish, (
					select rest_prices.name,rest_prices.id as r_id, max(rest_prices.price)
					from Restaurant as r, Dish as d, (
															select r.name,r.id, avg(d.price) as price
															from Restaurant as r, Dish as d
															where r.id= d.restaurant_id and r.city_id=$city_id and d.price is not NULL
															group by r.id) as rest_prices
					where r.id= d.restaurant_id and r.city_id=$city_id and d.price is not NULL ) as expensive_rest
where Dish.restaurant_id= expensive_rest.r_id and Dish.price is not NULL
group by Dish.section_name";

$result2 = $conn->query($sql2);
$row2 = $result2->fetch_assoc();
?>

<h1> * The most expensive restaurant in <?php echo(str_replace('_', ' ', $city)) ?> is <a
        href="rest.php?rest=<?php echo $row2["r_name"]; ?>&id=<?php echo $row2["r_id"]; ?>&city=<?php echo $city; ?>"><?php echo $row2["r_name"] ?> </a>*
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
